<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CutiController extends Controller
{
    /**
     * Menampilkan form pengajuan cuti dengan data sisa cuti dinamis.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // 1. Tentukan Jatah Cuti Total (Allowance)
        // Idealnya, nilai ini disimpan di database (misal: tabel users atau positions) atau file config.
        $totalCuti = [
            'tahunan' => 12,
            'sakit'   => 5,
        ];

        $user = Auth::user();
        $tahunIni = Carbon::now()->year;

        // 2. Ambil semua cuti user yang statusnya 'disetujui' pada tahun ini.
        $cutiDisetujui = Cuti::where('user_id', $user->id)
                             ->where('status', 'disetujui')
                             ->whereYear('tanggal_mulai', $tahunIni)
                             ->get();

        // 3. Hitung total hari cuti yang sudah terpakai.
        $cutiTerpakai = $this->hitungCutiTerpakai($cutiDisetujui);

        // 4. Hitung sisa cuti.
        $sisaCuti = [
            'tahunan' => $totalCuti['tahunan'] - $cutiTerpakai['tahunan'],
            'sakit'   => $totalCuti['sakit'] - $cutiTerpakai['sakit'],
        ];

        return view('users.cuti', [
            'title' => 'Pengajuan Cuti',
            'sisaCuti' => $sisaCuti,
            'totalCuti' => $totalCuti,
        ]);
    }

    /**
     * Menyimpan data pengajuan cuti baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $validatedData = $request->validate([
            'jenis_cuti' => 'required|in:tahunan,sakit',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string|max:2000',
            'lampiran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // max 2MB
        ]);

        // Proses upload lampiran jika ada
        $pathLampiran = null;
        if ($request->hasFile('lampiran')) {
            $pathLampiran = $request->file('lampiran')->store('lampiran_cuti', 'public');
        }

        // Simpan data ke database
        Cuti::create([
            'user_id' => Auth::id(),
            'status' => 'diajukan', // Status default
            'jenis_cuti' => $validatedData['jenis_cuti'],
            'tanggal_mulai' => $validatedData['tanggal_mulai'],
            'tanggal_selesai' => $validatedData['tanggal_selesai'],
            'alasan' => $validatedData['alasan'],
            'lampiran' => $pathLampiran,
        ]);

        return redirect()->route('dashboard')->with('success', 'Pengajuan cuti Anda telah berhasil dikirim!');
    }

    /**
     * Helper function untuk menghitung total hari cuti yang terpakai.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $cutiCollection
     * @return array
     */
    private function hitungCutiTerpakai($cutiCollection): array
    {
        $cutiTerpakai = ['tahunan' => 0, 'sakit' => 0];

        foreach ($cutiCollection as $cuti) {
            $tanggalMulai = Carbon::parse($cuti->tanggal_mulai);
            $tanggalSelesai = Carbon::parse($cuti->tanggal_selesai);
            $durasi = $tanggalMulai->diffInDays($tanggalSelesai) + 1;

            if (array_key_exists($cuti->jenis_cuti, $cutiTerpakai)) {
                $cutiTerpakai[$cuti->jenis_cuti] += $durasi;
            }
        }

        return $cutiTerpakai;
    }
}