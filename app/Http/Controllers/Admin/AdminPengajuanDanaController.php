<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengajuanDana;
use Illuminate\Support\Facades\Auth;

class AdminPengajuanDanaController extends Controller
{
    /**
     * Menampilkan daftar semua pengajuan dana.
     */
    public function index(Request $request)
    {
        $query = PengajuanDana::with('user');

        // Logika filter
        if ($request->filled('status')) {
            $query->where(function($q) use ($request) {
                $q->where('status_atasan', $request->status)
                  ->orWhere('status_hrd', $request->status)
                  ->orWhere('status_direktur', $request->status);
            });
        }
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('created_at', [$request->tanggal_mulai, $request->tanggal_akhir]);
        }

        $pengajuanDanas = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.pengajuan-dana.index', [
            'title' => 'Rekap Pengajuan Dana',
            'pengajuanDanas' => $pengajuanDanas,
        ]);
    }

    /**
     * Menampilkan halaman detail pengajuan dana untuk admin.
     */
    public function show(PengajuanDana $pengajuanDana)
    {
        $title = 'Detail Pengajuan Dana';
        return view('admin.pengajuan-dana.show', compact('pengajuanDana', 'title'));
    }
}