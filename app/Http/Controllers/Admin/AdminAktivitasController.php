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
        // --- Ambil Data Untuk Dropdown Filter ---
        $divisions = User::select('divisi')
            ->whereNotNull('divisi')
            ->where('divisi', '!=', '')
            ->distinct()
            ->orderBy('divisi')
            ->pluck('divisi');

        $users = User::orderBy('name')->get(['id', 'name']);

        // --- Proses Filter ---
        // UPDATE: Default range jadi seminggu (7 hari terakhir)
        $defaultStart = now()->subDays(6)->toDateString(); 
        $defaultEnd   = now()->toDateString();

        $startDate = $request->input('start_date', $defaultStart);
        $endDate   = $request->input('end_date', $defaultEnd);
        
        $divisi = $request->input('divisi');
        $userId = $request->input('user_id');

        // Query Builder
        $query = Aktivitas::with('user')
                    // Filter Range Tanggal
                    ->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate)
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

        // Pass startDate dan endDate ke view untuk mengisi value input date
        return view('admin.aktivitas.index', compact('aktivitasHarian', 'divisions', 'users', 'startDate', 'endDate', 'divisi', 'userId'));
    }

    /**
     * Download PDF Aktivitas berdasarkan filter.
     */
    public function downloadPdf(Request $request)
    {
        // UPDATE: Default range jadi seminggu (sama dengan index)
        $defaultStart = now()->subDays(6)->toDateString(); 
        $defaultEnd   = now()->toDateString();

        $startDate = $request->input('start_date', $defaultStart);
        $endDate   = $request->input('end_date', $defaultEnd);

        $divisi = $request->input('divisi');
        $userId = $request->input('user_id');

        // Query
        $query = Aktivitas::with('user')
                    ->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->orderBy('created_at', 'asc'); // Urutkan ASC (lama ke baru) untuk laporan PDF

        if ($divisi) {
            $query->whereHas('user', function ($q) use ($divisi) {
                $q->where('divisi', $divisi);
            });
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $aktivitas = $query->get();

        // Info Filter untuk Header PDF
        $filterInfo = 'Semua Karyawan';
        if($userId) {
            $user = User::find($userId);
            $filterInfo = $user ? $user->name : '-';
        } elseif($divisi) {
            $filterInfo = 'Divisi ' . $divisi;
        }

        $pdf = PDF::loadView('admin.aktivitas.pdf', compact('aktivitas', 'startDate', 'endDate', 'filterInfo'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('laporan_aktivitas_' . $startDate . '_sd_' . $endDate . '.pdf');
    }
}