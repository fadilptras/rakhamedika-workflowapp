<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\User;
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

        // --- LOGIKA FILTER ---

        // Filter Rentang Waktu
        if ($request->filled('filter_rentang')) {
            $rentang = $request->filter_rentang;
            if ($rentang == 'minggu_ini') {
                $query->whereBetween('tanggal', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($rentang == 'bulan_ini') {
                $query->whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year);
            }
        } 
        // Filter bulan dan tahun
        elseif ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereMonth('tanggal', $request->bulan)->whereYear('tanggal', $request->tahun);
        }
        // Filter tanggal
        elseif ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter divisi
        if ($request->filled('divisi')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('divisi', $request->divisi);
            });
        }

        // [BARU] Filter berdasarkan Karyawan (user_id)
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        $absensiRecords = $query->paginate(15)->withQueryString();

        // Data untuk dropdown filter di view
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $years = range(now()->year, now()->year - 5);
        $divisions = User::select('divisi')->whereNotNull('divisi')->distinct()->pluck('divisi');
        
        // [BARU] Ambil data semua karyawan untuk dropdown filter
        $users = User::where('role', 'user')->orderBy('name')->get();

        return view('admin.absensi.index', compact('title', 'absensiRecords', 'months', 'years', 'divisions', 'users'));
    }
}

