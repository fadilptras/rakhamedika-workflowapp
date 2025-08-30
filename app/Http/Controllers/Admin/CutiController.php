<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;

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
     * Mengubah status pengajuan cuti (Disetujui/Ditolak).
     */
    public function updateStatus(Request $request, Cuti $cuti)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
        ]);

        DB::transaction(function () use ($request, $cuti) {
            $cuti->update(['status' => $request->status]);

            // Jika status disetujui, tambahkan data ke tabel absensi
            if ($request->status == 'disetujui') {
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
                            'jam_masuk' => '00:00:00', // Atau null, sesuai kebutuhan
                        ]
                    );
                }
            }
        });

        return redirect()->route('admin.cuti.index')->with('success', 'Status pengajuan cuti berhasil diperbarui.');
    }
}