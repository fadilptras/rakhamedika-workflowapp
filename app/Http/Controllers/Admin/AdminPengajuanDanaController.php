<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanDana;
use App\Models\User; // Tambahkan ini untuk mengambil data karyawan
use Illuminate\Http\Request;

class AdminPengajuanDanaController extends Controller
{
    /**
     * Menampilkan daftar semua pengajuan dana dari user.
     */
    public function index(Request $request) // Tambahkan Request untuk menerima input filter
    {
        // Query dasar dengan relasi user
        $query = PengajuanDana::with('user');

        // Filter berdasarkan NAMA KARYAWAN
        if ($request->filled('karyawan_id')) {
            $query->where('user_id', $request->karyawan_id);
        }

        // Filter berdasarkan DIVISI
        if ($request->filled('divisi')) {
            $query->where('divisi', $request->divisi);
        }

        // Filter berdasarkan RANGE TANGGAL
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->start_date . ' 00:00:00';
            $endDate = $request->end_date . ' 23:59:59';
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        // Ambil data setelah difilter, urutkan dari yang terbaru
        $pengajuanDana = $query->latest()->get();

        // Ambil data untuk dropdown filter
        $karyawanList = User::orderBy('name')->get(); // Ambil semua user untuk dropdown
        $divisiList = PengajuanDana::select('divisi')->distinct()->whereNotNull('divisi')->orderBy('divisi')->get();

        return view('admin.pengajuan-dana.index', compact('pengajuanDana', 'karyawanList', 'divisiList'));
    }

    /**
     * Menampilkan detail pengajuan dana.
     */
    public function show(PengajuanDana $pengajuanDana)
    {
        // Cukup kirim data ke view
        return view('admin.pengajuan-dana.show', compact('pengajuanDana'));
    }
}