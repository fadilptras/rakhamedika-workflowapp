<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aktivitas;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AdminAktivitasController extends Controller
{
    /**
     * Menampilkan halaman daftar aktivitas karyawan.
     */
    public function index(Request $request)
    {
        // --- Ambil Data Untuk Filter ---
        $divisions = User::select('divisi')
            ->whereNotNull('divisi')
            ->where('divisi', '!=', '')
            ->distinct()
            ->orderBy('divisi')
            ->pluck('divisi');

        // Ambil semua user untuk dropdown filter
        $users = User::orderBy('name')->get(['id', 'name']);

        // --- Proses Filter ---
        $tanggal = $request->input('tanggal', now()->toDateString());
        $divisi = $request->input('divisi');
        $userId = $request->input('user_id');

        // Query Builder
        $query = Aktivitas::with('user')
                    ->whereDate('created_at', $tanggal)
                    ->orderBy('created_at', 'desc');

        // Filter Divisi
        if ($divisi) {
            $query->whereHas('user', function ($q) use ($divisi) {
                $q->where('divisi', $divisi);
            });
        }

        // Filter User (Perorangan)
        if ($userId) {
            $query->where('user_id', $userId);
        }

        // Ambil hasil akhir dengan paginasi
        $aktivitasHarian = $query->paginate(25)->withQueryString();

        return view('admin.aktivitas.index', compact('aktivitasHarian', 'divisions', 'users', 'tanggal', 'divisi', 'userId'));
    }

    /**
     * Download PDF Aktivitas berdasarkan filter.
     */
    public function downloadPdf(Request $request)
    {
        // Ambil input filter
        $tanggal = $request->input('tanggal', now()->toDateString());
        $divisi = $request->input('divisi');
        $userId = $request->input('user_id');

        // Query sama persis dengan index, tapi gunakan get() bukan paginate()
        $query = Aktivitas::with('user')
                    ->whereDate('created_at', $tanggal)
                    ->orderBy('created_at', 'asc'); // Urutkan ASC agar runut waktunya di PDF

        if ($divisi) {
            $query->whereHas('user', function ($q) use ($divisi) {
                $q->where('divisi', $divisi);
            });
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $aktivitas = $query->get();

        // Siapkan Judul Filter untuk ditampilkan di PDF
        $filterInfo = 'Semua Karyawan';
        if($userId) {
            $user = User::find($userId);
            $filterInfo = $user ? $user->name : '-';
        } elseif($divisi) {
            $filterInfo = 'Divisi ' . $divisi;
        }

        $pdf = PDF::loadView('admin.aktivitas.pdf', compact('aktivitas', 'tanggal', 'filterInfo'));

        // Set paper size (A4 Portrait biasanya cukup untuk list aktivitas)
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('laporan_aktivitas_' . $tanggal . '.pdf');
    }
}