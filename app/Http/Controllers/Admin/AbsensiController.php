<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    /**
     * halaman rekap absensi seluruh karyawan
     */
    public function index(Request $request)
    {
        $title = 'Rekap Absensi Karyawan';

        $query = Absensi::with('user')->latest();

        // --- LOGIKA FILTER ---
        if ($request->filled('filter_rentang')) {
            $rentang = $request->filter_rentang;
            if ($rentang == 'minggu_ini') {
                $query->whereBetween('tanggal', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($rentang == 'bulan_ini') {
                $query->whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year);
            }
        } 
        elseif ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereMonth('tanggal', $request->bulan)->whereYear('tanggal', $request->tahun);
        }
        elseif ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('divisi')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('divisi', $request->divisi);
            });
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        $absensiRecords = $query->paginate(15)->withQueryString();

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $years = range(now()->year, now()->year - 5);
        $divisions = User::select('divisi')->whereNotNull('divisi')->distinct()->pluck('divisi');
        
        $users = User::where('role', 'user')->orderBy('name')->get();

        return view('admin.absensi.index', compact('title', 'absensiRecords', 'months', 'years', 'divisions', 'users'));
    }

    /**
     * Method baru untuk download PDF.
     */
    public function downloadPDF(Request $request)
    {
        // Logika query filter disalin dari method index, tapi tanpa paginasi
        $query = Absensi::with('user')->latest();

        $periode = "Semua Waktu"; // Default

        // --- LOGIKA FILTER ---
        if ($request->filled('filter_rentang')) {
            $rentang = $request->filter_rentang;
            if ($rentang == 'minggu_ini') {
                $query->whereBetween('tanggal', [now()->startOfWeek(), now()->endOfWeek()]);
                $periode = "Minggu Ini";
            } elseif ($rentang == 'bulan_ini') {
                $query->whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year);
                $periode = "Bulan " . Carbon::now()->isoFormat('MMMM YYYY');
            }
        } 
        elseif ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereMonth('tanggal', $request->bulan)->whereYear('tanggal', $request->tahun);
            $periode = "Bulan " . Carbon::create()->month($request->bulan)->isoFormat('MMMM') . " " . $request->tahun;
        }
        elseif ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
             $periode = Carbon::parse($request->tanggal)->isoFormat('D MMMM YYYY');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('divisi')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('divisi', $request->divisi);
            });
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $absensiRecords = $query->get(); // Mengambil semua data yang terfilter

        $pdf = Pdf::loadView('admin.absensi.pdf', [
            'absensiRecords' => $absensiRecords,
            'periode' => $periode
        ]);

        return $pdf->download('rekap-absensi-'.now()->format('Y-m-d').'.pdf');
    }
}