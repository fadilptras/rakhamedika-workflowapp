<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Absensi;
use App\Models\Cuti;
use Carbon\Carbon;

class RekapAbsenController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);

        // Mengambil data absensi
        $absensi = Absensi::where('user_id', $user->id)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->orderBy('tanggal', 'desc')
            ->get();
            
        // Menghitung rekap dari data absensi yang sudah ada
        $rekap = [
            'hadir' => $absensi->where('status', 'hadir')->count(),
            'sakit' => $absensi->where('status', 'sakit')->count(),
            'izin' => $absensi->where('status', 'izin')->count(),
            'cuti' => $absensi->where('status', 'cuti')->count(),
        ];

        // Daftar bulan untuk dropdown filter
        $daftarBulan = [];
        for ($i = 1; $i <= 12; $i++) {
            $daftarBulan[$i] = Carbon::create()->month($i)->translatedFormat('F');
        }

        // Daftar tahun untuk dropdown filter
        $daftarTahun = range(Carbon::now()->year, Carbon::now()->year - 4);

        return view('users.rekap_absen', [
            'title' => 'Rekap Absensi Saya',
            'absensi' => $absensi,
            'rekap' => $rekap,
            'bulanDipilih' => $bulan,
            'tahunDipilih' => $tahun,
            'daftarBulan' => $daftarBulan,
            'daftarTahun' => $daftarTahun
        ]);
    }
}

