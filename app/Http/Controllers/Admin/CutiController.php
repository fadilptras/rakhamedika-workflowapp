<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Absensi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;

class CutiController extends Controller
{
    /**
     * Menampilkan daftar semua pengajuan cuti.
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
            'status' => 'required|in:disetujui,ditolak',
            'catatan_approval' => 'nullable|string',
        ]);

        $user = Auth::user();

        // Hanya role tertentu yang boleh melakukan aksi ini
        if (!in_array($user->jabatan, ['HRD', 'Manajer', 'Direktur'])) {
            return redirect()->route('admin.cuti.show', $cuti)->with('error', 'Anda tidak punya hak akses untuk aksi ini.');
        }

        // Jika status disetujui, buat record absensi 'cuti'
        if ($request->status === 'disetujui') {
            $period = CarbonPeriod::create($cuti->tanggal_mulai, $cuti->tanggal_selesai);
            foreach ($period as $date) {
                Absensi::updateOrCreate(
                    ['user_id' => $cuti->user_id, 'tanggal' => $date->format('Y-m-d')],
                    ['status' => 'cuti', 'keterangan' => 'Cuti ' . $cuti->jenis_cuti, 'jam_masuk' => '00:00:00']
                );
            }
        }
        
        // Simpan status dan catatan
        $cuti->update([
            'status' => $request->status === 'disetujui' ? 'diterima' : 'ditolak', // Gunakan 'diterima' agar sesuai dengan tampilan
            'catatan_approval' => $request->catatan_approval,
        ]);
        
        return redirect()->route('admin.cuti.show', $cuti)->with('success', 'Status pengajuan cuti berhasil diperbarui.');
    }
}