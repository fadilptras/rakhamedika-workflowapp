<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanBarang;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Notifications\PengajuanBarangNotification;
use Barryvdh\DomPDF\Facade\Pdf;

class PengajuanBarangController extends Controller
{
    public function index()
    {
        $title = 'Pengajuan Barang';
        $pengajuanBarangs = Auth::user()->pengajuanBarangs()->orderBy('created_at', 'desc')->get();
        return view('users.pengajuan-barang', compact('title', 'pengajuanBarangs'));
    }

    public function store(Request $request)
    {
        // 1. Validasi input dari form
        $request->validate([
            'judul_pengajuan' => 'required|string|max:255',
            'divisi' => 'required|string|max:255',
            'rincian_deskripsi.*' => 'required|string',
            'rincian_jumlah.*' => 'required|integer|min:1',
            'rincian_satuan.*' => 'required|string',
        ]);

        $user = Auth::user();

        // 2. Ambil ID approver dari data user agar variabel terdefinisi
        $app1 = $user->approver_barang_1_id;
        $app2 = $user->approver_barang_2_id;
        $app3 = $user->approver_barang_3_id;

        // 3. Identifikasi status awal: jika ID kosong, langsung tandai 'skipped'
        $st1 = $app1 ? 'menunggu' : 'skipped';
        $st2 = $app2 ? 'menunggu' : 'skipped';
        $st3 = $app3 ? 'menunggu' : 'skipped';

        // 4. Buat data pengajuan barang
        $pengajuan = PengajuanBarang::create([
            'user_id' => $user->id,
            'judul_pengajuan' => $request->judul_pengajuan,
            'divisi' => $request->divisi,
            'rincian_barang' => $this->parseRincian($request),
            'lampiran' => $this->uploadFiles($request),
            'status' => 'diajukan',
            'approver_barang_1_id' => $app1,
            'status_appr_1' => $st1,
            'approver_barang_2_id' => $app2,
            'status_appr_2' => $st2,
            'approver_barang_3_id' => $app3,
            'status_appr_3' => $st3,
        ]);

        // 5. Logika Notifikasi Pertama (Melompati yang kosong)
        // Mencari siapa yang harus menerima notifikasi pertama kali berdasarkan status 'menunggu'
        if ($st1 === 'menunggu') {
            $pengajuan->approver1->notify(new PengajuanBarangNotification($pengajuan, 'baru'));
        } elseif ($st2 === 'menunggu') {
            $pengajuan->approver2->notify(new PengajuanBarangNotification($pengajuan, 'baru'));
        } elseif ($st3 === 'menunggu') {
            $pengajuan->approver3->notify(new PengajuanBarangNotification($pengajuan, 'baru'));
        }

        return redirect()->route('pengajuan_barang.index')->with('success', 'Pengajuan berhasil dikirim.');
    }

    public function show(PengajuanBarang $pengajuanBarang)
    {
        return view('users.detail-pengajuan-barang', compact('pengajuanBarang'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'alasan' => 'nullable|string|max:255'
        ]);

        $pengajuanBarang = PengajuanBarang::with(['user', 'approver1', 'approver2', 'approver3'])->findOrFail($id);
        $user = Auth::user();
        $statusInput = $request->status;

        // --- LOGIKA APPROVER 1 ---
        if ($user->id == $pengajuanBarang->approver_barang_1_id && $pengajuanBarang->status_appr_1 == 'menunggu') {
            $pengajuanBarang->update([
                'status_appr_1' => $statusInput,
                'catatan_approver_1' => $request->alasan,
            ]);

            if ($statusInput == 'disetujui') {
                // Cari siapa berikutnya yang aktif
                if ($pengajuanBarang->approver_barang_2_id) {
                    $pengajuanBarang->approver2->notify(new PengajuanBarangNotification($pengajuanBarang, 'baru'));
                } elseif ($pengajuanBarang->approver_barang_3_id) {
                    $pengajuanBarang->update(['status_appr_2' => 'skipped']);
                    $pengajuanBarang->approver3->notify(new PengajuanBarangNotification($pengajuanBarang, 'baru'));
                } else {
                    $pengajuanBarang->update(['status' => 'selesai']);
                    $pengajuanBarang->user->notify(new PengajuanBarangNotification($pengajuanBarang, 'disetujui_final'));
                }
            } else {
                $pengajuanBarang->update(['status' => 'ditolak']);
                $pengajuanBarang->user->notify(new PengajuanBarangNotification($pengajuanBarang, 'ditolak'));
            }
        }

        // --- LOGIKA APPROVER 2 ---
        elseif ($user->id == $pengajuanBarang->approver_barang_2_id && $pengajuanBarang->status_appr_2 == 'menunggu') {
            $pengajuanBarang->update([
                'status_appr_2' => $statusInput,
                'catatan_approver_2' => $request->alasan,
            ]);

            if ($statusInput == 'disetujui') {
                if ($pengajuanBarang->approver_barang_3_id) {
                    $pengajuanBarang->approver3->notify(new PengajuanBarangNotification($pengajuanBarang, 'baru'));
                } else {
                    $pengajuanBarang->update(['status' => 'selesai']);
                    $pengajuanBarang->user->notify(new PengajuanBarangNotification($pengajuanBarang, 'disetujui_final'));
                }
            } else {
                $pengajuanBarang->update(['status' => 'ditolak']);
                $pengajuanBarang->user->notify(new PengajuanBarangNotification($pengajuanBarang, 'ditolak'));
            }
        }

        // --- LOGIKA APPROVER 3 (FINAL) ---
        elseif ($user->id == $pengajuanBarang->approver_barang_3_id && $pengajuanBarang->status_appr_3 == 'menunggu') {
            $pengajuanBarang->update([
                'status_appr_3' => $statusInput,
                'catatan_approver_3' => $request->alasan,
                'status' => ($statusInput == 'disetujui') ? 'selesai' : 'ditolak'
            ]);

            $pengajuanBarang->user->notify(new PengajuanBarangNotification($pengajuanBarang, ($statusInput == 'disetujui' ? 'disetujui_final' : 'ditolak')));
        } 
        
        else {
            return redirect()->back()->with('error', 'Otoritas tidak valid atau urutan persetujuan salah.');
        }

        return redirect()->back()->with('success', 'Status pengajuan barang berhasil diperbarui.');
    }


    private function notifyNext($pengajuan, $stage) {
        $nextId = $pengajuan->{"approver_barang_{$stage}_id"};
        if ($nextId) {
            User::find($nextId)->notify(new PengajuanBarangNotification($pengajuan, 'baru'));
        } else if ($stage < 3) {
            $this->notifyNext($pengajuan, $stage + 1); // Cek stage berikutnya jika yang ini null
        }
    }

    public function download(PengajuanBarang $pengajuanBarang)
    {
            $pdf = Pdf::loadView('pdf.pengajuan-barang', [
            'pengajuanBarang' => $pengajuanBarang,
            'approver1' => User::find($pengajuanBarang->approver_barang_1_id),
            'approver2' => User::find($pengajuanBarang->approver_barang_2_id),
            'approver3' => User::find($pengajuanBarang->approver_barang_3_id),
        ]);
        return $pdf->download('Pengajuan_'.$pengajuanBarang->id.'.pdf');
    }

/**
     * Fungsi Pembantu: Mengolah rincian barang dari form ke format array (JSON)
     */
    private function parseRincian(Request $request)
    {
        $rincian = [];
        if ($request->has('rincian_deskripsi')) {
            foreach ($request->rincian_deskripsi as $index => $deskripsi) {
                $rincian[] = [
                    'deskripsi' => $deskripsi,
                    'satuan' => $request->rincian_satuan[$index] ?? '-',
                    'jumlah' => $request->rincian_jumlah[$index] ?? 0,
                ];
            }
        }
        return $rincian;
    }

    /**
     * Fungsi Pembantu: Mengurus upload banyak file lampiran
     */
    private function uploadFiles(Request $request)
    {
        $pathFiles = [];
        if ($request->hasFile('file_pendukung')) {
            foreach ($request->file('file_pendukung') as $file) {
                // Simpan ke storage/app/public/lampiran_barang
                $pathFiles[] = $file->store('lampiran_barang', 'public');
            }
        }
        return $pathFiles;
    }    
}