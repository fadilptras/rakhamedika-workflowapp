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
        $today = Carbon::today('Asia/Jakarta');

        $absensiHariIni = Absensi::where('user_id', Auth::id())
                                ->where('tanggal', $today->toDateString())
                                ->first();

        // Kirim data ke view
        return view('absen', compact('title', 'absensiHariIni'));
    }

    /**
     * Menyimpan data absensi yang baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'status' => 'required|in:hadir,sakit,izin',
            'keterangan' => 'required_if:status,sakit,izin|max:1000',
            'lampiran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:1048', // max 1MB
        ]);

        $today = Carbon::today('Asia/Jakarta');
        $now = Carbon::now('Asia/Jakarta');

        // validasi absen
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
}
