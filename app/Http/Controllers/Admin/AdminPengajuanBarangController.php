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

        // [BARU] Logika Tabulasi Status
        $activeTab = $request->input('tab', 'pending'); 

        switch ($activeTab) {
            case 'pending':
                // Menampilkan yang sedang berjalan (Menunggu Atasan atau Diproses Gudang)
                $query->whereIn('status', ['diajukan', 'diproses']);
                break;
            case 'approved':
                // Menampilkan yang sudah selesai
                $query->where('status', 'selesai');
                break;
            case 'rejected':
                // Menampilkan yang gagal/batal
                $query->whereIn('status', ['ditolak', 'dibatalkan']);
                break;
            default:
                // Jika tab='all', tampilkan semua
                break;
        }

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
            'activeTab' => $activeTab // Kirim variable tab ke view
        ]);
    }

    /**
     * Menampilkan detail pengajuan barang.
     */
    public function show(PengajuanBarang $pengajuanBarang)
    {
        $pengajuanBarang->load(['user', 'approverAtasan', 'approverGudang']);

        return view('admin.pengajuan-barang.show', [
            'title' => 'Detail Pengajuan Barang',
            'pengajuanBarang' => $pengajuanBarang,
        ]);
    }

    /**
     * Download PDF Satuan.
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
        
        // [BARU] Terapkan logika Tab pada PDF juga
        $activeTab = $request->input('tab', 'pending'); 
        switch ($activeTab) {
            case 'pending':
                $query->whereIn('status', ['diajukan', 'diproses']);
                break;
            case 'approved':
                $query->where('status', 'selesai');
                break;
            case 'rejected':
                $query->whereIn('status', ['ditolak', 'dibatalkan']);
                break;
        }

        $startDate = null; $endDate = null;
        $karyawanId = $request->input('karyawan_id');
        $divisi = $request->input('divisi');

        // Filter Query
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
        
        $filename = "rekap-pengajuan-barang-" . strtoupper($activeTab) . "-" . Carbon::now()->format('Y-m-d') . ".pdf";
        return $pdf->download($filename);
    }
}