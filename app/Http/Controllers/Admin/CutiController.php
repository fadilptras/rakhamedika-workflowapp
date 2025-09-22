<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\User;
use Illuminate\Http\Request;

class CutiController extends Controller
{
    /**
     * Menampilkan daftar semua pengajuan cuti untuk dipantau.
     */
    public function index(Request $request)
    {
        $query = Cuti::with('user');
        $users = User::where('role', 'user')->orderBy('name')->get();

        // Logika filter
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jenis_cuti')) {
            $query->where('jenis_cuti', $request->jenis_cuti);
        }
        
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_mulai', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal_selesai', '<=', $request->tanggal_selesai);
        }

        $cutiRequests = $query->latest()->paginate(10);
        
        return view('admin.cuti.index', [
            'title' => 'Manajemen Pengajuan Cuti',
            'cutiRequests' => $cutiRequests,
            'users' => $users,
        ]);
    }
    
    /**
     * Menampilkan detail pengajuan cuti untuk admin (hanya lihat).
     */
    public function show(Cuti $cuti)
    {
        $title = 'Detail Pengajuan Cuti';
        return view('admin.cuti.show', compact('cuti', 'title'));
    }
}