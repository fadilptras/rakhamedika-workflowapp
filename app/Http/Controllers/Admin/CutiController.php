<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;

class CutiController extends Controller
{
    /**
     * Menampilkan daftar semua pengajuan cuti.
     */
    public function index()
    {
        $cutiRequests = Cuti::with('user')->latest()->paginate(10);
        return view('admin.cuti.index', [
            'title' => 'Manajemen Pengajuan Cuti',
            'cutiRequests' => $cutiRequests,
        ]);
    }
    
    /**
     * Menampilkan detail pengajuan cuti untuk admin.
     */
    public function show(Cuti $cuti)
    {
        $title = 'Detail Pengajuan Cuti';
        return view('admin.cuti.show', compact('cuti', 'title'));
    }

    /**
     * Mengubah status pengajuan cuti (disederhanakan untuk admin).
     * Admin (misal: HRD) mungkin perlu menolak cuti yang sudah terlanjur diterima.
     */
    public function updateStatus(Request $request, Cuti $cuti)
    {
        $request->validate([
            'status' => 'required|in:ditolak', // Admin hanya boleh menolak
            'catatan_penolakan' => 'required|string',
        ]);

        $user = Auth::user();

        // Hanya role tertentu (misal: HRD atau Manajer) yang boleh membatalkan
        if (!in_array($user->jabatan, ['HRD', 'Manajer'])) {
             return redirect()->route('admin.cuti.show', $cuti)->with('error', 'Anda tidak punya hak akses untuk aksi ini.');
        }

        // Simpan status penolakan
        $cuti->update([
            'status' => 'ditolak',
            'catatan_approval' => $request->catatan_penolakan,
        ]);

        // Hapus data absensi 'cuti' jika ada
        $period = CarbonPeriod::create($cuti->tanggal_mulai, $cuti->tanggal_selesai);
        foreach ($period as $date) {
            Absensi::where('user_id', $cuti->user_id)
                   ->where('tanggal', $date->format('Y-m-d'))
                   ->where('status', 'cuti')
                   ->delete();
        }
        
        return redirect()->route('admin.cuti.show', $cuti)->with('success', 'Pengajuan cuti telah berhasil dibatalkan/ditolak.');
    }
}