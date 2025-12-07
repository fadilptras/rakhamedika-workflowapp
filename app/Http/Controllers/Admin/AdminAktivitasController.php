<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aktivitas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminAktivitasController extends Controller
{
    /**
     * Menampilkan halaman daftar aktivitas karyawan.
     */
    public function index(Request $request)
    {
        // --- Ambil Data Untuk Filter ---
        
        // Ambil semua divisi unik, hilangkan null/kosong
        $divisions = User::select('divisi')
            ->whereNotNull('divisi')
            ->where('divisi', '!=', '')
            ->distinct()
            ->orderBy('divisi')
            ->pluck('divisi');

        // Ambil semua user untuk dropdown filter
        $users = User::orderBy('name')->get(['id', 'name']);

        // --- Proses Filter ---

        // Tentukan tanggal. Default-nya adalah hari ini.
        $tanggal = $request->input('tanggal', now()->toDateString());

        // Mulai query, load relasi 'user' agar efisien
        $query = Aktivitas::with('user')
                    ->whereDate('created_at', $tanggal)
                    ->orderBy('created_at', 'desc');

        // Filter berdasarkan Divisi
        if ($request->filled('divisi')) {
            $divisi = $request->input('divisi');
            $query->whereHas('user', function ($q) use ($divisi) {
                $q->where('divisi', $divisi);
            });
        }

        // Filter berdasarkan Karyawan
        if ($request->filled('user_id')) {
            $userId = $request->input('user_id');
            $query->where('user_id', $userId);
        }

        // Ambil hasil akhir dengan paginasi
        $aktivitasHarian = $query->paginate(25)->withQueryString();

        // Kirim data ke view
        return view('admin.aktivitas.index', [
            'title' => 'Aktivitas Karyawan',
            'aktivitasHarian' => $aktivitasHarian,
            'divisions' => $divisions,
            'users' => $users,
            'filters' => $request->only(['tanggal', 'divisi', 'user_id']), // Untuk menyimpan nilai filter
        ]);
    }
}