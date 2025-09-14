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

        // 1. Cek absensi user hari ini
        $absensiHariIni = Absensi::where('user_id', $user->id)
            ->where('tanggal', $today->toDateString())
            ->first();

        // 2. Cek absensi lembur hari ini
        $lemburHariIni = Lembur::where('user_id', $user->id)
            ->where('tanggal', $today->toDateString())
            ->first();

        // 3. Rekap absensi bulanan
        $absensiBulanIni = Absensi::where('user_id', $user->id)
            ->whereYear('tanggal', $today->year)
            ->whereMonth('tanggal', $today->month)
            ->get();

        $rekapAbsen = [
            'hadir' => $absensiBulanIni->where('status', 'hadir')->count(),
            'sakit' => $absensiBulanIni->where('status', 'sakit')->count(),
            'izin'  => $absensiBulanIni->where('status', 'izin')->count(),
        ];

        // Hitung total keterlambatan
        $totalLateMinutes = 0;
        $standardWorkHourCarbon = Carbon::parse('08:00:00');

        foreach ($absensiBulanIni as $record) {
            if ($record->status == 'hadir' && $record->jam_masuk) {
                $jamMasuk = Carbon::parse($record->jam_masuk);
                if ($jamMasuk->gt($standardWorkHourCarbon)) {
                    $totalLateMinutes += $jamMasuk->diffInMinutes($standardWorkHourCarbon);
                }
            }
        }
        $rekapAbsen['terlambat'] = \Carbon\CarbonInterval::minutes($totalLateMinutes)->cascade()->forHumans(['short' => true]);

        // Hitung total cuti
        $totalCutiTerpakai = Cuti::where('user_id', $user->id)
            ->where('status', 'disetujui')
            ->whereYear('tanggal_mulai', $today->year)
            ->sum(DB::raw('DATEDIFF(tanggal_selesai, tanggal_mulai) + 1'));
        $rekapAbsen['cuti'] = $totalCutiTerpakai;

        // 4. Daftar rekan satu divisi & status absensinya hari ini
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
                ->where('tanggal', $today->toDateString())
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

        return view('users.absen', compact('title', 'absensiHariIni', 'lemburHariIni', 'rekapAbsen', 'daftarRekan'));
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

        if (in_array($request->status, ['sakit', 'izin'])) {
            if (!$request->filled('keterangan') && !$request->hasFile('lampiran')) {
                return redirect()->back()->withInput()
                    ->withErrors(['keterangan' => 'Untuk Sakit/Izin, wajib isi keterangan atau lampiran.']);
            }
        }
        
        if ($request->status === 'hadir' && (!$request->latitude || !$request->longitude)) {
            return redirect()->back()->with('error', 'Lokasi GPS wajib diaktifkan untuk absen hadir.');
        }

        $pathLampiran = null;
        if ($request->hasFile('lampiran')) {
            $pathLampiran = $request->file('lampiran')->store('lampiran_absensi', 'public');
        }

        Absensi::create([
            'user_id'   => Auth::id(),
            'tanggal'   => today()->toDateString(),
            'jam_masuk' => now()->toTimeString(),
            'status'    => $request->status,
            'keterangan'=> $request->keterangan,
            'lampiran'  => $pathLampiran,
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
        ]);

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
            'keterangan_keluar'  => 'nullable|string|max:1000',
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
            'keterangan_keluar' => $request->keterangan_keluar,
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
            'keterangan' => 'nullable|string|max:1000',
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
            return response()->json(['error' => 'Anda harus absen pulang terlebih dahulu untuk memulai lembur.'], 403);
        }

        if (Lembur::where('user_id', $user->id)->where('tanggal', $today)->exists()) {
            return response()->json(['error' => 'Anda sudah melakukan absensi lembur masuk hari ini.'], 403);
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

        return response()->json(['success' => 'Absensi lembur masuk berhasil direkam!']);
    }

    /**
     * Menyimpan data absensi keluar lembur.
     */
    public function updateLemburKeluar(Request $request, Lembur $lembur)
    {
        // Validasi data yang diterima dari form
        $request->validate([
            // Tidak ada keterangan keluar di form absen keluar lembur, jadi hapus validasi ini jika tidak digunakan
            // 'keterangan_keluar' => 'nullable|string|max:1000',
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