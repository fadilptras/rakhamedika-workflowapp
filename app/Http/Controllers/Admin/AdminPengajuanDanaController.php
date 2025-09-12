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

    /**
     * Menyetujui pengajuan dana.
     */
    public function approve(Request $request, PengajuanDana $pengajuanDana)
    {
        $userJabatan = strtolower(Auth::user()->jabatan);
        if (!in_array($userJabatan, ['atasan', 'hrd', 'direktur'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk menyetujui pengajuan dana.');
        }

        $statusField = 'status_' . $userJabatan;
        $catatanField = 'catatan_' . $userJabatan;

        $pengajuanDana->update([
            $statusField => 'disetujui',
            $catatanField => $request->catatan_persetujuan ?? 'Disetujui',
        ]);
        
        // Cek apakah semua pihak sudah menyetujui
        if ($pengajuanDana->status_atasan === 'disetujui' && $pengajuanDana->status_hrd === 'disetujui' && $pengajuanDana->status_direktur === 'disetujui') {
            $pengajuanDana->update(['status' => 'disetujui']);
        }

        return redirect()->route('admin.pengajuan_dana.show', $pengajuanDana->id)->with('success', 'Pengajuan dana berhasil disetujui!');
    }

    /**
     * Menolak pengajuan dana.
     */
    public function reject(Request $request, PengajuanDana $pengajuanDana)
    {
        $userJabatan = strtolower(Auth::user()->jabatan);
        if (!in_array($userJabatan, ['atasan', 'hrd', 'direktur'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk menolak pengajuan dana.');
        }
        
        $statusField = 'status_' . $userJabatan;
        $catatanField = 'catatan_' . $userJabatan;

        $pengajuanDana->update([
            $statusField => 'ditolak',
            $catatanField => $request->catatan_penolakan ?? 'Ditolak',
            'status' => 'ditolak'
        ]);

        return redirect()->route('admin.pengajuan_dana.show', $pengajuanDana->id)->with('success', 'Pengajuan dana berhasil ditolak!');
    }
}