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
     * Mengubah status pengajuan cuti.
     */
    public function updateStatus(Request $request, Cuti $cuti)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan_persetujuan' => 'nullable|string',
            'catatan_penolakan' => 'nullable|string',
        ]);

        $user = Auth::user();
        $catatan = $request->status === 'disetujui' ? $request->catatan_persetujuan : $request->catatan_penolakan;

        // Validasi dan perbarui status berdasarkan jabatan pengguna
        if ($user->jabatan === 'Manajer' && $cuti->status_manajer === 'diajukan') {
            $cuti->update([
                'status_manajer' => $request->status,
                'catatan_manajer' => $catatan ?? ($request->status === 'disetujui' ? 'Disetujui' : 'Ditolak'),
            ]);

            // Jika manajer menolak, langsung ubah status akhir menjadi 'ditolak'
            if ($request->status === 'ditolak') {
                $cuti->update(['status' => 'ditolak']);
            }
            
            return redirect()->route('admin.cuti.show', $cuti)->with('success', 'Status pengajuan cuti berhasil diperbarui oleh Manajer.');
        }

        // Jika HRD, cek apakah manajer sudah menyetujui
        if ($user->jabatan === 'HRD' && $cuti->status_manajer === 'disetujui' && $cuti->status_hrd === 'diajukan') {
            $cuti->update([
                'status_hrd' => $request->status,
                'catatan_hrd' => $catatan ?? ($request->status === 'disetujui' ? 'Disetujui' : 'Ditolak'),
            ]);

            // Perbarui status akhir
            if ($request->status === 'disetujui') {
                 $cuti->update(['status' => 'diterima']);
                 
                 // Otomatisasi absensi jika HRD menyetujui
                 $period = CarbonPeriod::create($cuti->tanggal_mulai, $cuti->tanggal_selesai);
                 foreach ($period as $date) {
                     Absensi::updateOrCreate(
                         [
                             'user_id' => $cuti->user_id,
                             'tanggal' => $date->format('Y-m-d'),
                         ],
                         [
                             'status' => 'cuti',
                             'keterangan' => 'Cuti ' . $cuti->jenis_cuti . ': ' . $cuti->alasan,
                             'jam_masuk' => '00:00:00',
                         ]
                     );
                 }
            } else {
                $cuti->update(['status' => 'ditolak']);
            }
            
            return redirect()->route('admin.cuti.show', $cuti)->with('success', 'Status pengajuan cuti berhasil diperbarui oleh HRD.');
        }

        // Jika tidak ada kondisi yang terpenuhi
        return redirect()->route('admin.cuti.show', $cuti)->with('error', 'Aksi tidak diizinkan atau pengajuan cuti belum pada tahap ini.');
    }
}