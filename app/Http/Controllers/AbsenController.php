<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\User;
use App\Models\Cuti;
use App\Models\Lembur;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\CarbonPeriod;

class AbsenController extends Controller
{
    /**
     * Menampilkan halaman absensi.
     */
    public function absen()
    {
        $title = 'Form Absensi';
        $user = Auth::user();
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $isWeekend = $today->isWeekend();

        // Cek apakah ada absensi yang belum selesai dari hari sebelumnya (tanpa jam keluar)
        $unfinishedAbsensi = Absensi::where('user_id', $user->id)
            ->where('tanggal', $yesterday->toDateString())
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_keluar')
            ->first();

        // Siapkan variabel default
        $absensiHariIni = null;
        $lemburHariIni = null;

        // Ambil data absensi hari ini, terlepas dari apakah itu akhir pekan atau tidak.
        // Dengan ini, pengguna tetap bisa absen di hari libur.
        $absensiHariIni = Absensi::where('user_id', $user->id)
            ->where('tanggal', $today->toDateString())
            ->first();

        $lemburHariIni = Lembur::where('user_id', $user->id)
            ->where('tanggal', $today->toDateString())
            ->first();

        $rekapAbsen = $this->rekapAbsensiBulanan($user, $today);
        $daftarRekan = $this->getDaftarRekan($user, $today);

        // Karena kita tidak memblokir absen, logika ini perlu diubah untuk mengakomodasi absen di hari libur.
        $absensiKemarin = Absensi::where('user_id', $user->id)
            ->where('tanggal', $today->subDay()->toDateString())
            ->where('status', 'hadir')
            ->first();
        
        // Perbaikan: Hapus pengecekan absensiKemarin jika ingin absen di hari libur tetap bisa dilakukan.
        // Cukup cek unfinishedAbsensi saja.
        $unfinishedAbsensi = Absensi::where('user_id', $user->id)
            ->where('tanggal', $yesterday->toDateString())
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_keluar')
            ->first();

        return view('users.absen', compact('title', 'absensiHariIni', 'lemburHariIni', 'rekapAbsen', 'daftarRekan', 'unfinishedAbsensi', 'isWeekend'));
    }

    /**
     * Metode helper untuk menghitung rekap absensi bulanan.
     */
    protected function rekapAbsensiBulanan(User $user, Carbon $date): array
    {
        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();

        // 1. Ambil semua data relevan sekaligus
        $absensiDalamPeriode = Absensi::where('user_id', $user->id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->keyBy(fn($item) => Carbon::parse($item->tanggal)->toDateString());

        $cutiDalamPeriode = Cuti::where('user_id', $user->id)
            ->where('status', 'disetujui')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where('tanggal_mulai', '<=', $endDate)
                      ->where('tanggal_selesai', '>=', $startDate);
            })
            ->get();

        // 2. Inisialisasi rekap
        $rekap = [
            'hadir' => 0,
            'sakit' => 0,
            'izin'  => 0,
            'cuti'  => 0,
            'tidak hadir' => 0, // Ini akan menjadi 'alpa'
            'terlambat' => 0 // Dalam menit
        ];
        $standardWorkHour = Carbon::createFromTime(8, 0, 0, 'Asia/Jakarta');
        
        // 3. Iterasi setiap hari dalam sebulan
        $period = CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $day) {
            if ($day->isWeekend()) {
                continue; // Lewati akhir pekan
            }

            $tanggalFormatted = $day->toDateString();
            $recordAbsensi = $absensiDalamPeriode->get($tanggalFormatted);

            $isOnLeave = $cutiDalamPeriode->first(function ($cuti) use ($day) {
                return $day->between(Carbon::parse($cuti->tanggal_mulai), Carbon::parse($cuti->tanggal_selesai));
            });

            if ($isOnLeave) {
                $rekap['cuti']++;
            } elseif ($recordAbsensi) {
                $status = strtolower($recordAbsensi->status);
                if (array_key_exists($status, $rekap)) {
                    $rekap[$status]++;
                }
                
                if ($status === 'hadir' && $recordAbsensi->jam_masuk) {
                    $jamMasuk = Carbon::parse($recordAbsensi->jam_masuk, 'Asia/Jakarta');
                    if ($jamMasuk->gt($standardWorkHour)) {
                        $diffInMinutes = abs($jamMasuk->diffInMinutes($standardWorkHour));
                        $rekap['terlambat'] += $diffInMinutes;
                    }
                }
            } else {
                // Karyawan tidak cuti dan tidak ada catatan absensi
                // Jika tanggal sudah lewat, hitung sebagai 'tidak hadir' (alpa)
                if ($day->isPast() && !$day->isToday()) {
                    $rekap['tidak hadir']++;
                }
            }
        }
        
        // 4. Format waktu terlambat
        $totalMenitTerlambat = $rekap['terlambat'];
        $jamTerlambat = floor($totalMenitTerlambat / 60);
        $menitTerlambat = $totalMenitTerlambat % 60;
        $rekap['terlambat'] = $jamTerlambat . ' Jam ' . $menitTerlambat . ' Menit';

        return $rekap;
    }


    /**
     * Metode helper untuk mendapatkan daftar rekan satu divisi dan status absensi mereka.
     */
    protected function getDaftarRekan(User $user, Carbon $date): array
    {
        $daftarRekan = [];
        $jabatanUser = $user->jabatan;

        if (in_array($jabatanUser, ['HRD', 'Manajer', 'Direktur'])) {
            $rekanDilihat = User::where('id', '!=', $user->id)->get();
        } else if ($user->divisi) {
            $rekanDilihat = User::where('divisi', $user->divisi)
                ->where('id', '!=', $user->id)
                ->get();
        } else {
            $rekanDilihat = collect();
        }

        if ($rekanDilihat->isNotEmpty()) {
            $absensiRekanHariIni = Absensi::whereIn('user_id', $rekanDilihat->pluck('id'))
                ->where('tanggal', $date->toDateString())
                ->get()
                ->keyBy('user_id');

            foreach ($rekanDilihat as $rekan) {
                $statusAbsensi = $absensiRekanHariIni->get($rekan->id);
                $daftarRekan[] = (object) [
                    'user'   => $rekan,
                    'status' => $statusAbsensi ? $statusAbsensi->status : 'Belum Absen'
                ];
            }
        }
        return $daftarRekan;
    }

    /**
     * Menyimpan data absensi masuk.
     */
    public function store(Request $request)
    {
        $request->validate([
            'status'     => 'required|in:hadir,sakit,izin',
            'keterangan' => 'nullable|string|max:1000',
            'lampiran'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'latitude'   => 'nullable|string|max:255',
            'longitude'  => 'nullable|string|max:255',
        ]);

        if (Absensi::where('user_id', Auth::id())->where('tanggal', today()->toDateString())->exists()) {
            return redirect()->route('absen')->with('error', 'Anda sudah melakukan absensi hari ini.');
        }

        // Logika baru: Cek absensi hari sebelumnya
        $absensiKemarin = Absensi::where('user_id', Auth::id())
            ->where('tanggal', today()->subDay()->toDateString())
            ->where('status', 'hadir')
            ->first();

        if ($absensiKemarin && is_null($absensiKemarin->jam_keluar)) {
            return redirect()->route('absen')->with('error', 'Anda belum melakukan absen keluar pada hari kerja sebelumnya.');
        }

        // Cek validasi untuk sakit/izin dan hadir 
        if (in_array($request->status, ['sakit', 'izin'])) {
            if (!$request->filled('keterangan') && !$request->hasFile('lampiran')) {
                return redirect()->back()->withInput()
                    ->withErrors(['keterangan' => 'Untuk Sakit/Izin, wajib isi keterangan atau lampiran.']);
            }
        }
        
        if ($request->status === 'hadir' && (!$request->latitude || (!$request->longitude))) {
            return redirect()->back()->with('error', 'Lokasi GPS wajib diaktifkan untuk absen hadir.');
        }

        $pathLampiran = null;
        if ($request->hasFile('lampiran')) {
            $pathLampiran = $request->file('lampiran')->store('lampiran_absensi', 'public');
        }

        $inputStatus = $request->status;
        $jamMasuk = now();
        $notPresentThreshold = Carbon::parse('23:59:00', 'Asia/Jakarta');
        $standardWorkHour = Carbon::parse('08:00:00', 'Asia/Jakarta');
        $keterangan = $request->keterangan;

        // Logika untuk menentukan status akhir
        if ($inputStatus === 'hadir') {
            
            if ($jamMasuk->gt($notPresentThreshold)) {
                $inputStatus = 'tidak hadir';
                $keterangan = null; // Hapus keterangan jika statusnya 'tidak_hadir'
            }
            
            else if ($jamMasuk->gt($standardWorkHour)) {
                $diffInMinutes = abs($jamMasuk->diffInMinutes($standardWorkHour));
                $jamTerlambat = floor($diffInMinutes / 60);
                $menitTerlambat = $diffInMinutes % 60;
                $keteranganTerlambat = "Terlambat {$jamTerlambat} Jam {$menitTerlambat} Menit.";
                $keterangan = trim($keteranganTerlambat . ' ' . ($keterangan ? 'Keterangan: ' . $keterangan : ''));
            }
        }

        Absensi::create([
            'user_id'   => Auth::id(),
            'tanggal'   => today()->toDateString(),
            'jam_masuk' => $jamMasuk->toTimeString(),
            'status'    => $inputStatus, // Gunakan status yang sudah diperbarui
            'keterangan'=> $keterangan,
            'lampiran'  => $pathLampiran,
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        // Tambahkan pesan yang lebih spesifik jika statusnya diubah
        if ($inputStatus === 'tidak hadir') {
            return redirect()->route('absen')->with('error', 'Absensi masuk Anda melewati batas waktu (11:00). Status Anda otomatis menjadi Tidak Hadir.');
        }

        return redirect()->route('absen')->with('success', 'Absensi berhasil direkam!');
    }

    /**
     * Menyimpan data absensi keluar.
     */
    public function updateKeluar(Request $request, Absensi $absensi)
    {
        $request->validate([
            'lampiran_keluar'    => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'latitude_keluar'    => 'required|string',
            'longitude_keluar'   => 'required|string',
        ]);

        if ($absensi->user_id !== Auth::id()) {
            return back()->with('error', 'Aksi tidak diizinkan.');
        }

        // Simpan lampiran
        $pathLampiranKeluar = null;
        if ($request->hasFile('lampiran_keluar')) {
            $pathLampiranKeluar = $request->file('lampiran_keluar')->store('lampiran_absensi_keluar', 'public');
        }

        $absensi->update([
            'jam_keluar'        => now()->toTimeString(),
            'lampiran_keluar'   => $pathLampiranKeluar,
            'latitude_keluar'   => $request->latitude_keluar,
            'longitude_keluar'  => $request->longitude_keluar,
        ]);

        return redirect()->route('absen')->with('success', 'Absensi keluar berhasil direkam!');
    }

    /**
     * Menyimpan data absensi masuk lembur.
     */
    public function storeLembur(Request $request)
    {
        $request->validate([
            'keterangan' => 'required|string|max:1000',
            'lampiran_masuk'    => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'latitude_masuk'    => 'required|string|max:255',
            'longitude_masuk'   => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $today = today()->toDateString();
        $absensiHariIni = Absensi::where('user_id', $user->id)
                                         ->where('tanggal', $today)
                                         ->first();

        if (!$absensiHariIni || !$absensiHariIni->jam_keluar) {
            return redirect()->route('absen')->with('error', 'Anda harus absen pulang terlebih dahulu untuk memulai lembur.');
        }

        if (Lembur::where('user_id', $user->id)->where('tanggal', $today)->exists()) {
           return redirect()->route('absen')->with('error', 'Anda sudah melakukan absensi lembur masuk hari ini.');
        }

        $pathLampiranMasuk = null;
        if ($request->hasFile('lampiran_masuk')) {
            $pathLampiranMasuk = $request->file('lampiran_masuk')->store('lampiran_lembur', 'public');
        }

        Lembur::create([
            'user_id'   => $user->id,
            'tanggal'   => $today,
            'jam_masuk_lembur' => now()->toTimeString(),
            'keterangan'=> $request->keterangan,
            'lampiran_masuk'   => $pathLampiranMasuk,
            'latitude_masuk'   => $request->latitude_masuk,
            'longitude_masuk'  => $request->longitude_masuk,
        ]);

        return redirect()->route('absen')->with('success', 'Absensi lembur masuk berhasil direkam!');
    }

    /**
     * Menyimpan data absensi keluar lembur.
     */
    public function updateLemburKeluar(Request $request, Lembur $lembur)
    {
        // Validasi data yang diterima dari form
        $request->validate([
            'lampiran_keluar'    => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'latitude_keluar'    => 'required|string',
            'longitude_keluar'   => 'required|string',
        ]);
    
        // Cek otorisasi pengguna
        if ($lembur->user_id !== Auth::id()) {
            return redirect()->route('absen')->with('error', 'Aksi tidak diizinkan.');
        }
        
        // Cek apakah sudah absen keluar lembur
        if ($lembur->jam_keluar_lembur) {
            return redirect()->route('absen')->with('error', 'Anda sudah melakukan absensi keluar lembur.');
        }
        
        $pathLampiranKeluar = null;
        if ($request->hasFile('lampiran_keluar')) {
            $pathLampiranKeluar = $request->file('lampiran_keluar')->store('lampiran_lembur_keluar', 'public');
        }
    
        // Update data lembur
        $lembur->update([
            'jam_keluar_lembur' => now()->toTimeString(),
            'lampiran_keluar'    => $pathLampiranKeluar,
            'latitude_keluar'    => $request->latitude_keluar,
            'longitude_keluar'   => $request->longitude_keluar,
        ]);
    
        return redirect()->route('absen')->with('success', 'Absen keluar lembur berhasil direkam. Terima kasih!');
    }
}