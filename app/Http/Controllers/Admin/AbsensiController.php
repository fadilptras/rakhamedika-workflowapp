<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    /**
     * Menampilkan halaman rekapitulasi absensi seluruh karyawan.
     */
    public function index(Request $request)
    {
        $title = 'Rekap Absensi Karyawan';

        $query = Absensi::with('user')->latest();

        // --- LOGIKA FILTER BARU ---

        // Filter Rentang Waktu (Mingguan/Bulanan)
        if ($request->filled('filter_rentang')) {
            $rentang = $request->filter_rentang;
            if ($rentang == 'minggu_ini') {
                $query->whereBetween('tanggal', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($rentang == 'bulan_ini') {
                $query->whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year);
            }
        } 
        // Filter berdasarkan bulan dan tahun yang dipilih
        elseif ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereMonth('tanggal', $request->bulan)->whereYear('tanggal', $request->tahun);
        }
        // Filter berdasarkan tanggal spesifik (harian)
        elseif ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        // Filter berdasarkan status (tetap berfungsi)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $absensiRecords = $query->paginate(15)->withQueryString();

        // Data untuk dropdown filter di view
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $years = range(now()->year, now()->year - 5); // 5 tahun ke belakang

        return view('admin.absensi.index', compact('title', 'absensiRecords', 'months', 'years'));
    }
}
