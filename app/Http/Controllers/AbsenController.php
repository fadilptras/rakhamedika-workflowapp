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
            // Lampiran wajib jika status hadir. Untuk status lain, opsional.
            'lampiran' => 'required_if:status,hadir|nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            // Latitude dan Longitude wajib jika status hadir.
            'latitude' => 'required_if:status,hadir|nullable|string|max:255',
            'longitude' => 'required_if:status,hadir|nullable|string|max:255',
        ]);

        // Validasi custom: Jika status sakit atau izin, keterangan atau lampiran wajib diisi.
        if (in_array($request->status, ['sakit', 'izin'])) {
            if (!$request->filled('keterangan') && !$request->hasFile('lampiran')) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'keterangan' => 'Untuk status Sakit atau Izin, Keterangan atau Lampiran wajib diisi.',
                        'lampiran' => ' '
                    ]);
            }
        }

        $today = Carbon::today();
        $now = Carbon::now();

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
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return redirect()->route('dashboard')->with('success', 'Terima kasih, absensi Anda berhasil direkam!');
    }

    /**
     * Menyimpan data absensi keluar (Absen Keluar).
     */
    public function updateKeluar(Request $request, Absensi $absensi)
    {
        if ($absensi->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Aksi tidak diizinkan.');
        }

        $request->validate([
            'keterangan_keluar' => 'required|string|max:1000',
        ]);

        $absensi->update([
            'jam_keluar' => Carbon::now()->toTimeString(),
            'keterangan_keluar' => $request->keterangan_keluar,
        ]);

        return redirect()->route('absen')->with('success', 'Absen keluar berhasil direkam. Terima kasih!');
    }
}