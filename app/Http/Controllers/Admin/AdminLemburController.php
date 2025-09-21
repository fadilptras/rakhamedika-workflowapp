<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lembur;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminLemburController extends Controller
{
    /**
     * Menampilkan rekap lembur karyawan.
     */
    public function index(Request $request)
    {
        // Mengambil bulan, tahun, divisi, user, dan hari dari request atau menggunakan nilai default
        $month = intval($request->input('month', now()->month));
        $year = intval($request->input('year', now()->year));
        $day = intval($request->input('day', now()->day));
        $divisi = $request->input('divisi');

        // Mengambil semua divisi dan user untuk dropdown filter
        $divisions = User::select('divisi')->whereNotNull('divisi')->distinct()->pluck('divisi');

        // Buat objek Carbon untuk bulan dan tahun yang dipilih
        $dateForDays = Carbon::createFromDate($year, $month, 1);

        // Query dasar untuk mengambil data dari model Lembur
        $query = Lembur::with('user');
        
        // Menambahkan filter opsional jika ada
        if ($divisi) {
            $query->whereHas('user', function ($q) use ($divisi) {
                $q->where('divisi', $divisi);
            });
        }
        
        // Filter berdasarkan hari jika ada, jika tidak, filter berdasarkan bulan dan tahun
        if ($day) {
            $query->whereDate('tanggal', $dateForDays->day($day)->format('Y-m-d'));
        } else {
             $query->whereBetween('tanggal', [$dateForDays->startOfMonth(), $dateForDays->endOfMonth()]);
        }

        $lemburRecords = $query->latest('tanggal')->paginate(15);
        
        // Data untuk dropdown filter bulan dan tahun
        $months = collect(range(1, 12))->mapWithKeys(function ($bulan) {
            return [$bulan => Carbon::create()->month($bulan)->translatedFormat('F')];
        });
        $years = range(now()->year, now()->year - 5);
        $daysInMonth = $dateForDays->daysInMonth;

        return view('admin.lembur.index', [
            'title' => 'Rekap Lembur Karyawan',
            'lemburRecords' => $lemburRecords,
            'divisions' => $divisions,
            'months' => $months,
            'years' => $years,
            'month' => $month,
            'year' => $year,
            'day' => $day,
            'divisi' => $divisi,
            'daysInMonth' => $daysInMonth
        ]);
    }
    
    /**
     * Download rekap lembur sebagai PDF.
     */
    public function downloadPdf(Request $request)
    {
        $month = intval($request->input('month', now()->month));
        $year = intval($request->input('year', now()->year));
        $day = intval($request->input('day', now()->day));
        $divisi = $request->input('divisi');

        $dateForDays = Carbon::createFromDate($year, $month, 1);

        $query = Lembur::with('user');
        
        if ($divisi) {
            $query->whereHas('user', function ($q) use ($divisi) {
                $q->where('divisi', $divisi);
            });
        }
        
        if ($day) {
            $query->whereDate('tanggal', $dateForDays->day($day)->format('Y-m-d'));
        } else {
             $query->whereBetween('tanggal', [$dateForDays->startOfMonth(), $dateForDays->endOfMonth()]);
        }

        $lemburRecords = $query->latest('tanggal')->get();

        $pdf = PDF::loadView('admin.lembur.pdf', compact('lemburRecords', 'dateForDays'));
        
        $filename = 'rekap_lembur_'. ($day ? $dateForDays->day($day)->format('Y-m-d') : $dateForDays->isoFormat('MMMM_YYYY')) .'.pdf';
        return $pdf->download($filename);
    }
}