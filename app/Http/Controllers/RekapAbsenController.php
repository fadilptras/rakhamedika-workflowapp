<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Cuti;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RekapAbsenController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Rekap Absensi Saya';
        $user = Auth::user();

        // Ambil filter dari request, atau gunakan nilai default (bulan & tahun sekarang)
        $bulanDipilih = $request->input('bulan', now()->month);
        $tahunDipilih = $request->input('tahun', now()->year);

        // Query dasar untuk absensi, hanya untuk user yang sedang login
        $queryAbsensi = Absensi::where('user_id', $user->id)
            ->whereYear('tanggal', $tahunDipilih)
            ->whereMonth('tanggal', $bulanDipilih);

        // Ambil semua data absensi untuk periode terpilih
        $absensi = $queryAbsensi->orderBy('tanggal', 'asc')->get();

        // Hitung rekap bulanan
        $rekap = [
            'hadir' => $absensi->where('status', 'hadir')->count(),
            'sakit' => $absensi->where('status', 'sakit')->count(),
            'izin'  => $absensi->where('status', 'izin')->count(),
        ];
        
        // Logika penghitungan cuti yang lebih akurat
        $totalCutiTerpakai = Cuti::where('user_id', $user->id)
            ->where('status', 'disetujui')
            ->where(function ($query) use ($tahunDipilih, $bulanDipilih) {
                $query->where(function($q) use ($tahunDipilih, $bulanDipilih) {
                    $q->whereYear('tanggal_mulai', $tahunDipilih)->whereMonth('tanggal_mulai', $bulanDipilih);
                })->orWhere(function($q) use ($tahunDipilih, $bulanDipilih) {
                    $q->whereYear('tanggal_selesai', $tahunDipilih)->whereMonth('tanggal_selesai', $bulanDipilih);
                });
            })
            ->get()
            ->sum(function ($cuti) use ($bulanDipilih, $tahunDipilih) {
                $start = Carbon::parse($cuti->tanggal_mulai);
                $end = Carbon::parse($cuti->tanggal_selesai);
                $days = 0;
                for ($date = $start; $date->lte($end); $date->addDay()) {
                    if ($date->month == $bulanDipilih && $date->year == $tahunDipilih) {
                        $days++;
                    }
                }
                return $days;
            });
        $rekap['cuti'] = $totalCutiTerpakai;

        // Data untuk dropdown filter bulan dan tahun
        $daftarBulan = collect(range(1, 12))->mapWithKeys(function ($bulan) {
            return [$bulan => Carbon::create()->month($bulan)->translatedFormat('F')];
        });
        $daftarTahun = range(now()->year, now()->year - 5);

        return view('users.rekap_absen', compact(
            'title',
            'absensi',
            'rekap',
            'bulanDipilih',
            'tahunDipilih',
            'daftarBulan',
            'daftarTahun'
        ));
    }
}