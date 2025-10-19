<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanDana;
use App\Models\User;
use Illuminate\Http\Request;
use PDF; // 1. Tambahkan use PDF di sini

class AdminPengajuanDanaController extends Controller
{
    /**
     * Menampilkan daftar semua pengajuan dana dari user.
     */
    public function index(Request $request)
    {
        // ... (kode yang sudah ada tidak perlu diubah)
        $query = PengajuanDana::with('user');

        if ($request->filled('karyawan_id')) {
            $query->where('user_id', $request->karyawan_id);
        }

        if ($request->filled('divisi')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('divisi', $request->divisi);
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->start_date . ' 00:00:00';
            $endDate = $request->end_date . ' 23:59:59';
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        $pengajuanDana = $query->latest()->get();

        $karyawanList = User::orderBy('name')->get();
        $divisiList = User::select('divisi')->distinct()->whereNotNull('divisi')->orderBy('divisi')->get();

        return view('admin.pengajuan-dana.index', compact('pengajuanDana', 'karyawanList', 'divisiList'));
    }

    /**
     * Menampilkan detail pengajuan dana.
     */
    public function show(PengajuanDana $pengajuanDana)
    {
        $pengajuanDana->load(['user', 'atasanApprover', 'direkturApprover', 'financeApprover']);
        return view('admin.pengajuan-dana.show', compact('pengajuanDana'));
    }

    /**
     * 2. Tambahkan method baru untuk download PDF di bawah ini
     */
    public function downloadPDF(PengajuanDana $pengajuanDana)
    {
        // Load semua relasi yang dibutuhkan agar datanya muncul di PDF
        $pengajuanDana->load(['user', 'atasanApprover', 'direkturApprover', 'financeApprover']);

        // Data dikirim ke view PDF yang sama dengan milik user
        $pdf = PDF::loadView('users.pdf_pengajuan_dana', compact('pengajuanDana'));

        // Buat nama file yang dinamis
        $namaJudul = \Illuminate\Support\Str::slug($pengajuanDana->judul_pengajuan, '-');
        $filename = "pengajuan-dana-{$pengajuanDana->id}-{$namaJudul}.pdf";

        // Tawarkan file untuk diunduh oleh browser
        return $pdf->download($filename);
    }
}