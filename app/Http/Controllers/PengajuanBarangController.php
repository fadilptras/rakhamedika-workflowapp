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
        $request->validate([
            'judul_pengajuan' => 'required|string|max:255',
            'divisi' => 'required|string|max:255',
            'rincian_deskripsi.*' => 'required|string',
            'rincian_jumlah.*' => 'required|integer|min:1',
            'rincian_satuan.*' => 'required|string',
        ]);

        $user = Auth::user();

        // Olah Rincian Barang
        $rincian = [];
        foreach ($request->rincian_deskripsi as $key => $val) {
            $rincian[] = [
                'nama_barang' => $val,
                'jumlah' => $request->rincian_jumlah[$key],
                'satuan' => $request->rincian_satuan[$key],
                'keperluan' => $request->rincian_keperluan[$key] ?? '-',
            ];
        }

        // Olah Lampiran (Multi-upload)
        $pathFiles = [];
        if ($request->hasFile('file_pendukung')) {
            foreach ($request->file('file_pendukung') as $file) {
                $pathFiles[] = $file->store('pengajuan-barang', 'public');
            }
        }

        $pengajuan = PengajuanBarang::create([
            'user_id' => $user->id,
            'judul_pengajuan' => $request->judul_pengajuan,
            'divisi' => $request->divisi,
            'rincian_barang' => $rincian,
            'lampiran' => $pathFiles,
            'status' => 'diajukan',
            // Logika Approver Otomatis
            'approver_1_id' => $user->approver_barang_1_id,
            'status_appr_1' => $user->approver_barang_1_id ? 'menunggu' : 'skipped',
            'approver_2_id' => $user->approver_barang_2_id,
            'status_appr_2' => $user->approver_barang_2_id ? 'menunggu' : 'skipped',
        ]);

        $pengajuan->load('user');

        if ($user->approver_barang_1_id) {
            User::find($user->approver_barang_1_id)->notify(new PengajuanBarangNotification($pengajuan, 'baru'));
        }

        return redirect()->route('pengajuan_barang.index')->with('success', 'Pengajuan berhasil dikirim.');
    }

    public function show(PengajuanBarang $pengajuanBarang)
    {
        return view('users.detail-pengajuan-barang', compact('pengajuanBarang'));
    }

    public function updateStatus(Request $request, PengajuanBarang $pengajuanBarang)
    {
        $user = Auth::user();
        $status = $request->status; // 'disetujui' / 'ditolak'

        if ($user->id == $pengajuanBarang->approver_1_id) {
            $pengajuanBarang->update([
                'status_appr_1' => $status,
                'catatan_approver_1' => $request->alasan,
                'tanggal_approved_1' => now(),
            ]);

            if ($status == 'disetujui' && $pengajuanBarang->approver_2_id) {
                User::find($pengajuanBarang->approver_2_id)->notify(new PengajuanBarangNotification($pengajuanBarang, 'disetujui_atasan'));
            }
        } elseif ($user->id == $pengajuanBarang->approver_2_id) {
            $pengajuanBarang->update([
                'status_appr_2' => $status,
                'catatan_approver_2' => $request->alasan,
                'tanggal_approved_2' => now(),
            ]);
        }

        // Final Status Logic
        if ($status == 'ditolak') {
            $pengajuanBarang->update(['status' => 'ditolak']);
        } elseif ($pengajuanBarang->status_appr_1 == 'disetujui' && ($pengajuanBarang->status_appr_2 == 'disetujui' || $pengajuanBarang->status_appr_2 == 'skipped')) {
            $pengajuanBarang->update(['status' => 'selesai']);
        }

        return redirect()->back()->with('success', 'Status berhasil diperbarui.');
    }

    public function download(PengajuanBarang $pengajuanBarang)
    {
        $pdf = Pdf::loadView('pdf.pengajuan-barang', [
            'pengajuanBarang' => $pengajuanBarang,
            'approver1' => User::find($pengajuanBarang->approver_1_id),
            'approver2' => User::find($pengajuanBarang->approver_2_id),
        ]);
        return $pdf->download('Pengajuan_'.$pengajuanBarang->id.'.pdf');
    }
}