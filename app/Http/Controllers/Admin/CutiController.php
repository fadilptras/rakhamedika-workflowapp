<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Absensi;
use App\Models\User; // Ditambahkan
use Illuminate\Http\Request; // Ditambahkan
use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;

class CutiController extends Controller
{
    /**
     * Menampilkan daftar semua pengajuan cuti dengan filter.
     */
    public function index(Request $request) // Request ditambahkan
    {
        $query = Cuti::with('user')->latest();

        // Logika Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal_mulai', [$request->tanggal_mulai, $request->tanggal_akhir]);
        }

        $cutiRequests = $query->paginate(10)->withQueryString(); // withQueryString() agar filter tetap aktif saat pindah halaman
        $users = User::where('role', 'user')->orderBy('name')->get();

        return view('admin.cuti.index', [
            'title' => 'Manajemen Pengajuan Cuti',
            'cutiRequests' => $cutiRequests,
            'users' => $users, // Kirim data user untuk filter
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
     * Mengubah status pengajuan cuti.
     */
    public function updateStatus(Request $request, Cuti $cuti)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan_persetujuan' => 'nullable|string|max:1000',
            'catatan_penolakan' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $userJabatan = strtolower($user->jabatan);
        $catatan = $request->status === 'disetujui' ? $request->catatan_persetujuan : $request->catatan_penolakan;

        if ($userJabatan === 'manajer' && $cuti->status_manajer === 'diajukan') {
            $cuti->update([
                'status_manajer' => $request->status,
                'catatan_manajer' => $catatan ?? ($request->status === 'disetujui' ? 'Disetujui oleh Manajer' : 'Ditolak oleh Manajer'),
            ]);

            if ($request->status === 'ditolak') {
                $cuti->update(['status' => 'ditolak']);
            }
            
            return redirect()->route('admin.cuti.show', $cuti)->with('success', 'Status pengajuan cuti berhasil diperbarui oleh Manajer.');
        }

        if ($userJabatan === 'hrd' && $cuti->status_manajer === 'disetujui' && $cuti->status_hrd === 'diajukan') {
            $cuti->update([
                'status_hrd' => $request->status,
                'catatan_hrd' => $catatan ?? ($request->status === 'disetujui' ? 'Disetujui oleh HRD' : 'Ditolak oleh HRD'),
            ]);

            $cuti->update(['status' => $request->status]);

            if ($request->status === 'disetujui') {
                 $period = CarbonPeriod::create($cuti->tanggal_mulai, $cuti->tanggal_selesai);
                 foreach ($period as $date) {
                     Absensi::updateOrCreate(
                         ['user_id' => $cuti->user_id, 'tanggal' => $date->format('Y-m-d')],
                         ['status' => 'cuti', 'keterangan' => 'Cuti ' . $cuti->jenis_cuti . ': ' . $cuti->alasan, 'jam_masuk' => '00:00:00']
                     );
                 }
            }
            
            return redirect()->route('admin.cuti.show', $cuti)->with('success', 'Status pengajuan cuti berhasil diperbarui oleh HRD.');
        }

        return redirect()->route('admin.cuti.show', $cuti)->with('error', 'Aksi tidak diizinkan atau pengajuan cuti belum pada tahap ini.');
    }

    /**
     * Menampilkan halaman pengaturan jatah cuti untuk admin.
     */
    public function pengaturanCuti()
    {
        $users = User::where('role', 'user')->get(); // Ambil karyawan saja
        return view('admin.cuti.pengaturan', [
            'title' => 'Pengaturan Jatah Cuti',
            'users' => $users
        ]);
    }

    /**
     * Menyimpan perubahan jatah cuti.
     */
    public function updatePengaturanCuti(Request $request)
    {
        $request->validate([
            'jatah_cuti' => 'required|array',
            'jatah_cuti.*' => 'required|integer|min:0',
        ]);

        foreach ($request->jatah_cuti as $userId => $jatahCuti) {
            $user = User::find($userId);
            if ($user) {
                $user->jatah_cuti = $jatahCuti;
                $user->save();
            }
        }

        return redirect()->route('admin.cuti.pengaturan')->with('success', 'Jatah cuti berhasil diperbarui.');
    }
}