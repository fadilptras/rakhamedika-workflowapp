<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AbsenController extends Controller
{
    /**
     * Menampilkan halaman form absen atau status jika sudah absen.
     */
    public function absen()
    {
        $title = 'Form Absensi';
        $today = Carbon::today();

        $absensiHariIni = Absensi::where('user_id', Auth::id())
                                 ->where('tanggal', $today->toDateString())
                                 ->first();

        // Logika untuk mengambil rekap absensi bulan ini
        $bulanIni = Carbon::now();
        $absensiBulanIni = Absensi::where('user_id', Auth::id())
                                    ->whereYear('tanggal', $bulanIni->year)
                                    ->whereMonth('tanggal', $bulanIni->month)
                                    ->get();

        $rekapAbsen = [
            'hadir' => $absensiBulanIni->where('status', 'hadir')->count(),
            'sakit' => $absensiBulanIni->where('status', 'sakit')->count(),
            'izin'  => $absensiBulanIni->where('status', 'izin')->count(),
        ];

        return view('users.absen', compact('title', 'absensiHariIni', 'rekapAbsen'));
    }

    /**
     * Menyimpan data absensi yang baru (Absen Masuk).
     */
    public function store(Request $request)
    {
        $request->validate([
            'status' => 'required|in:hadir,sakit,izin',
            'keterangan' => 'nullable|string|max:1000',
            'lampiran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:1048', // max 1MB
        ]);

        // Validasi custom: Jika status sakit atau izin, keterangan atau lampiran wajib diisi.
        if (in_array($request->status, ['sakit', 'izin'])) {
            if (!$request->filled('keterangan') && !$request->hasFile('lampiran')) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'keterangan' => 'Untuk status Sakit atau Izin, Keterangan atau Lampiran wajib diisi.',
                        'lampiran' => ' ' // Memberi pesan error agar border merah muncul
                    ]);
            }
        }

        $today = Carbon::today();
        $now = Carbon::now();

        // Validasi apakah pengguna sudah absen hari ini
        $sudahAbsen = Absensi::where('user_id', Auth::id())
                            ->where('tanggal', $today->toDateString())
                            ->exists();

        if ($sudahAbsen) {
            return redirect()->route('absen')->with('error', 'Anda sudah melakukan absensi hari ini.');
        }

        $pathLampiran = null;
        if ($request->hasFile('lampiran')) {
            $pathLampiran = $request->file('lampiran')->store('lampiran_absensi', 'public');
        }

        Absensi::create([
            'user_id' => Auth::id(),
            'tanggal' => $today->toDateString(),
            'jam_masuk' => $now->toTimeString(),
            'status' => $request->status,
            'keterangan' => $request->keterangan,
            'lampiran' => $pathLampiran,
        ]);

        return redirect()->route('dashboard')->with('success', 'Terima kasih, absensi Anda berhasil direkam!');
    }

    /**
     * Menyimpan data absensi keluar (Absen Keluar).
     */
    public function updateKeluar(Request $request, Absensi $absensi)
    {
        // Pastikan pengguna yang login adalah pemilik absensi
        if ($absensi->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Aksi tidak diizinkan.');
        }

        // Validasi
        $request->validate([
            'keterangan_keluar' => 'required|string|max:1000',
        ]);

        // Update data absensi
        $absensi->update([
            'jam_keluar' => Carbon::now()->toTimeString(),
            'keterangan_keluar' => $request->keterangan_keluar,
        ]);

        return redirect()->route('absen')->with('success', 'Absen keluar berhasil direkam. Terima kasih!');
    }
}

