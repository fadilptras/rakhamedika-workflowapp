<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; 

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

        // 1. Cek absensi user yang sedang login hari ini
        $absensiHariIni = Absensi::where('user_id', $user->id)
                                 ->where('tanggal', $today->toDateString())
                                 ->first();

        // 2. Ambil rekap absensi bulanan user yang login
        $absensiBulanIni = Absensi::where('user_id', $user->id)
                                    ->whereYear('tanggal', $today->year)
                                    ->whereMonth('tanggal', $today->month)
                                    ->get();
        $rekapAbsen = [
            'hadir' => $absensiBulanIni->where('status', 'hadir')->count(),
            'sakit' => $absensiBulanIni->where('status', 'sakit')->count(),
            'izin'  => $absensiBulanIni->where('status', 'izin')->count(),
        ];

        // 3. Logika baru: Ambil semua user satu divisi, lalu gabungkan dengan status absensi mereka
        $daftarRekan = [];
        if ($user->divisi) {
            // Ambil semua user dalam divisi yang sama (kecuali diri sendiri)
            $rekanSatuDivisi = User::where('divisi', $user->divisi)
                                   ->where('id', '!=', $user->id)
                                   ->get();

            // Ambil data absensi untuk semua rekan tersebut HANYA untuk hari ini
            $absensiRekanHariIni = Absensi::whereIn('user_id', $rekanSatuDivisi->pluck('id'))
                                          ->where('tanggal', $today->toDateString())
                                          ->get()
                                          ->keyBy('user_id'); // Jadikan user_id sebagai kunci untuk pencarian mudah

            // Gabungkan data user dengan data absensinya
            foreach ($rekanSatuDivisi as $rekan) {
                $statusAbsensi = $absensiRekanHariIni->get($rekan->id);
                $daftarRekan[] = (object)[
                    'user' => $rekan,
                    'status' => $statusAbsensi ? $statusAbsensi->status : 'Belum Absen'
                ];
            }
        }

        return view('users.absen', compact('title', 'absensiHariIni', 'rekapAbsen', 'daftarRekan'));
    }

    /**
     * Menyimpan data absensi masuk.
     */
    public function store(Request $request)
    {
        $request->validate([
            'status' => 'required|in:hadir,sakit,izin',
            'keterangan' => 'nullable|string|max:1000',
            'lampiran' => 'required_if:status,hadir|nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'latitude' => 'required_if:status,hadir|nullable|string|max:255',
            'longitude' => 'required_if:status,hadir|nullable|string|max:255',
        ]);

        if (in_array($request->status, ['sakit', 'izin'])) {
            if (!$request->filled('keterangan') && !$request->hasFile('lampiran')) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['keterangan' => 'Untuk status Sakit atau Izin, Keterangan atau Lampiran wajib diisi.']);
            }
        }

        if (Absensi::where('user_id', Auth::id())->where('tanggal', today()->toDateString())->exists()) {
            return redirect()->route('absen')->with('error', 'Anda sudah melakukan absensi hari ini.');
        }

        $pathLampiran = null;
        if ($request->hasFile('lampiran')) {
            $pathLampiran = $request->file('lampiran')->store('lampiran_absensi', 'public');
        }

        Absensi::create([
            'user_id' => Auth::id(),
            'tanggal' => today()->toDateString(),
            'jam_masuk' => now()->toTimeString(),
            'status' => $request->status,
            'keterangan' => $request->keterangan,
            'lampiran' => $pathLampiran,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return redirect()->route('absen')->with('success', 'Terima kasih, absensi Anda berhasil direkam!');
    }

    /**
     * Menyimpan data absensi keluar.
     */
    public function updateKeluar(Request $request, Absensi $absensi)
    {
        // Validasi data yang diterima
        $request->validate([
            'lampiran_keluar'   => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'latitude_keluar'   => 'required|string',
            'longitude_keluar'  => 'required|string',
            'keterangan_keluar' => 'nullable|string|max:1000', // Tambahkan validasi untuk keterangan keluar
        ]);

        if ($absensi->user_id !== Auth::id()) {
            return response()->json(['error' => 'Aksi tidak diizinkan.'], 403);
        }

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

        return response()->json(['success' => 'Absen keluar berhasil direkam. Terima kasih!']);
    }
}