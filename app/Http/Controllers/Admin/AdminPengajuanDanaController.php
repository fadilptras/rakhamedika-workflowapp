<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanDana;
use App\Models\User;
use Illuminate\Http\Request;
use PDF; // Pastikan use PDF sudah ada

class AdminPengajuanDanaController extends Controller
{
    // ... (method index() dan show() tidak berubah)
    public function index(Request $request) 
    {
        $query = PengajuanDana::with('user');

        if ($request->filled('karyawan_id')) {
            $query->where('user_id', $request->karyawan_id);
        }

        if ($request->filled('divisi')) {
            $query->where('divisi', $request->divisi);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->start_date . ' 00:00:00';
            $endDate = $request->end_date . ' 23:59:59';
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        $pengajuanDana = $query->latest()->get();

        $karyawanList = User::orderBy('name')->get(); 
        $divisiList = PengajuanDana::select('divisi')->distinct()->whereNotNull('divisi')->orderBy('divisi')->get();

        return view('admin.pengajuan-dana.index', compact('pengajuanDana', 'karyawanList', 'divisiList'));
    }

    public function show(PengajuanDana $pengajuanDana)
    {
        return view('admin.pengajuan-dana.show', compact('pengajuanDana'));
    }

    public function downloadPDF(PengajuanDana $pengajuanDana)
    {
        $pengajuanDana->load(['user', 'atasanApprover', 'direkturApprover', 'financeApprover']);
        $pdf = PDF::loadView('users.pdf_pengajuan_dana', compact('pengajuanDana'));
        $namaJudul = \Illuminate\Support\Str::slug($pengajuanDana->judul_pengajuan, '-');
        $filename = "pengajuan-dana-{$pengajuanDana->id}-{$namaJudul}.pdf";
        return $pdf->download($filename);
    }

    /**
     * TAMBAHKAN METHOD BARU DI BAWAH INI
     * Untuk men-download rekap PDF berdasarkan filter.
     */
    public function downloadRekapPDF(Request $request)
    {
        // Logika filter disamakan dengan method index()
        $query = PengajuanDana::with('user');

        if ($request->filled('karyawan_id')) {
            $query->where('user_id', $request->karyawan_id);
        }

        if ($request->filled('divisi')) {
            $query->where('divisi', $request->divisi);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->start_date . ' 00:00:00';
            $endDate = $request->end_date . ' 23:59:59';
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $pengajuanDana = $query->latest()->get();

        // Siapkan data filter untuk ditampilkan di header PDF
        $karyawanName = $request->filled('karyawan_id') ? User::find($request->karyawan_id)->name : 'Semua Karyawan';
        $divisiName = $request->filled('divisi') ? $request->divisi : 'Semua Divisi';
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        
        // Load view PDF dengan data yang sudah difilter
        $pdf = PDF::loadView('admin.pengajuan-dana.pdf_rekap', compact('pengajuanDana', 'karyawanName', 'divisiName', 'startDate', 'endDate'));

        // Buat nama file dan download
        $filename = 'rekap-pengajuan-dana-' . now()->format('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }
}