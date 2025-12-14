<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanBarang;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AdminPengajuanBarangController extends Controller
{
    /**
     * Menampilkan daftar pengajuan barang.
     */
    public function index(Request $request)
    {
        $query = PengajuanBarang::with('user')->latest();

        // Filter Karyawan
        if ($request->filled('karyawan_id')) {
            $query->where('user_id', $request->karyawan_id);
        }
        // Filter Divisi
        if ($request->filled('divisi')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('divisi', $request->divisi);
            });
        }
        // Filter Tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $pengajuanBarangs = $query->paginate(10)->appends($request->query());

        // Data untuk Dropdown Filter
        $karyawanList = User::where('role', 'user')->orderBy('name')->get();
        $divisiList = User::where('role', 'user')
                            ->whereNotNull('divisi')
                            ->select('divisi')
                            ->distinct()
                            ->orderBy('divisi')
                            ->get();

        return view('admin.pengajuan-barang.index', [
            'title' => 'Kelola Pengajuan Barang',
            'pengajuanBarangs' => $pengajuanBarangs,
            'karyawanList' => $karyawanList,
            'divisiList' => $divisiList,
        ]);
    }

    /**
     * Menampilkan detail pengajuan barang.
     */
    public function show(PengajuanBarang $pengajuanBarang)
    {
        // Load relasi approver (Atasan & Gudang)
        $pengajuanBarang->load(['user', 'approverAtasan', 'approverGudang']);

        return view('admin.pengajuan-barang.show', [
            'title' => 'Detail Pengajuan Barang',
            'pengajuanBarang' => $pengajuanBarang,
        ]);
    }

    /**
     * Download PDF Satuan (Menggunakan view yang sama dengan User).
     */
    public function downloadPDF(PengajuanBarang $pengajuanBarang)
    {
        $pengajuanBarang->load(['user', 'approverAtasan', 'approverGudang']);
        
        $pdf = Pdf::loadView('pdf.pengajuan-barang', compact('pengajuanBarang'));
        $pdf->setPaper('a4', 'portrait');
        
        $namaJudul = \Illuminate\Support\Str::slug($pengajuanBarang->judul_pengajuan, '-');
        return $pdf->download("Form-Barang-{$pengajuanBarang->id}-{$namaJudul}.pdf");
    }

    /**
     * Download Rekap PDF (Laporan Bulanan/Filter).
     */
    public function downloadRekapPDF(Request $request)
    {
        $query = PengajuanBarang::with('user')->latest();
        $startDate = null; $endDate = null;
        $karyawanId = $request->input('karyawan_id');
        $divisi = $request->input('divisi');

        // Filter Query (Sama seperti index)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        if ($karyawanId) {
            $query->where('user_id', $karyawanId);
        }
        if ($divisi) {
            $query->whereHas('user', function($q) use ($divisi) {
                $q->where('divisi', $divisi);
            });
        }

        $pengajuanBarangs = $query->get();

        // Info untuk Header PDF
        $karyawanName = 'Semua Karyawan';
        if ($karyawanId) {
            $karyawan = User::find($karyawanId);
            $karyawanName = $karyawan ? $karyawan->name : 'Semua Karyawan';
        }
        $divisiName = $divisi ?: 'Semua Divisi';

        $pdf = Pdf::loadView('admin.pengajuan-barang.pdf_rekap', compact(
            'pengajuanBarangs', 
            'startDate', 
            'endDate', 
            'karyawanName', 
            'divisiName'
        ));
        
        $filename = "rekap-pengajuan-barang-" . Carbon::now()->format('Y-m-d') . ".pdf";
        return $pdf->download($filename);
    }
}