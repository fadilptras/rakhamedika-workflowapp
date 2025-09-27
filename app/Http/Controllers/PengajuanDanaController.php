<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanDana;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Notifications\PengajuanDanaNotification;
use Illuminate\Support\Facades\Notification;

class PengajuanDanaController extends Controller
{
    public function index()
    {
        $title = 'Pengajuan Dana';
        $pengajuanDanas = Auth::user()->pengajuanDanas()->orderBy('created_at', 'desc')->get();
        return view('users.pengajuan-dana', compact('title', 'pengajuanDanas'));
    }

    public function show(PengajuanDana $pengajuanDana)
    {
        $user = Auth::user();
        $pemohon = $pengajuanDana->user;
        $isOwner = $user->id === $pemohon->id;

        $kepalaDivisi = User::where('divisi', $pemohon->divisi)
                              ->where('is_kepala_divisi', true)
                              ->first();
        
        $isKepalaDivisi = $kepalaDivisi && $user->id === $kepalaDivisi->id;
        $hasAllowedJabatan = in_array($user->jabatan, ['Kepala Finance dan Gudang', 'HRD', 'Direktur']);

        if (!$isOwner && !$isKepalaDivisi && !$hasAllowedJabatan) {
            abort(403, 'ANDA TIDAK MEMILIKI AKSES UNTUK MELIHAT HALAMAN INI.');
        }

        return view('users.detail-pengajuan-dana', [
            'title' => 'Detail Pengajuan Dana',
            'pengajuanDana' => $pengajuanDana,
        ]);
    }
    
    public function store(Request $request)
    {
        if ($request->has('jumlah_dana_total')) {
            $request->merge(['jumlah_dana_total' => preg_replace('/[^0-9]/', '', $request->jumlah_dana_total)]);
        }
        if ($request->has('rincian_jumlah')) {
            $cleanedRincian = [];
            foreach ($request->rincian_jumlah as $jumlah) {
                $cleanedRincian[] = preg_replace('/[^0-9]/', '', $jumlah);
            }
            $request->merge(['rincian_jumlah' => $cleanedRincian]);
        }

        $validatedData = $request->validate([
            'judul_pengajuan' => 'required|string|max:255',
            'divisi' => 'required|string|max:255',
            'nama_bank' => 'required_if:nama_bank_lainnya,null|nullable|string|max:255',
            'nama_bank_lainnya' => 'required_if:nama_bank,other|nullable|string|max:255',
            'no_rekening' => 'required|string|max:255',
            'jumlah_dana_total' => 'required|numeric|min:1',
            'rincian_deskripsi.*' => 'required|string|max:1000',
            'rincian_jumlah.*' => 'required|numeric|min:1',
            'file_pendukung' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);
    
        $rincian = [];
        if (!empty($validatedData['rincian_deskripsi'])) {
            foreach ($validatedData['rincian_deskripsi'] as $key => $deskripsi) {
                $rincian[] = ['deskripsi' => $deskripsi, 'jumlah' => $validatedData['rincian_jumlah'][$key]];
            }
        }
    
        $pathFile = null;
        if ($request->hasFile('file_pendukung')) {
            $pathFile = $request->file('file_pendukung')->store('lampiran_dana', 'public');
        }
    
        $pengajuanDana = PengajuanDana::create([
            'user_id' => Auth::id(),
            'judul_pengajuan' => $validatedData['judul_pengajuan'],
            'divisi' => $validatedData['divisi'],
            'nama_bank' => $validatedData['nama_bank'] === 'other' ? $validatedData['nama_bank_lainnya'] : $validatedData['nama_bank'],
            'no_rekening' => $validatedData['no_rekening'],
            'total_dana' => $validatedData['jumlah_dana_total'],
            'rincian_dana' => $rincian,
            'lampiran' => $pathFile,
        ]);
        
        $user = Auth::user();
        $kepalaDivisi = User::where('divisi', $user->divisi)->where('is_kepala_divisi', true)->first();
        $hrd = User::where('jabatan', 'HRD')->first();

        if ($kepalaDivisi) {
            Notification::send($kepalaDivisi, new PengajuanDanaNotification($pengajuanDana));
        } else {
            $direktur = User::where('jabatan', 'Direktur')->first();
            if ($direktur) {
                Notification::send($direktur, new PengajuanDanaNotification($pengajuanDana));
            }
        }
        if ($hrd) {
            Notification::send($hrd, new PengajuanDanaNotification($pengajuanDana));
        }

        return redirect()->route('pengajuan_dana.index')->with('success', 'Pengajuan dana berhasil dikirim!');
    }

    public function approve(Request $request, PengajuanDana $pengajuanDana)
    {
        // Otorisasi sekarang hanya satu baris, memanggil aturan di Policy
        $this->authorize('approve', $pengajuanDana);
        
        $user = Auth::user();
        $updateData = [];

        // Cek apakah user adalah Kepala Divisi dari divisi yang mengajukan
        if ($user->is_kepala_divisi && $user->divisi === $pengajuanDana->divisi) {
            $updateData['status_atasan'] = 'disetujui';
            $updateData['catatan_atasan'] = $request->catatan_persetujuan ?? 'Disetujui';

            // Cari Kepala Finance secara dinamis
            $kepalaFinance = User::where('divisi', 'Finance dan Gudang')->where('is_kepala_divisi', true)->first();
            if ($kepalaFinance) {
                $tempPengajuan = $pengajuanDana->replicate()->fill($updateData);
                Notification::send($kepalaFinance, new PengajuanDanaNotification($tempPengajuan));
            }

        } else { // Jika bukan Kepala Divisi (berarti Kepala Finance)
            $updateData['status_finance'] = 'disetujui';
            $updateData['catatan_finance'] = $request->catatan_persetujuan ?? 'Disetujui';
        }
        
        // Cari ID Kepala Finance secara dinamis untuk pengecekan
        $kepalaFinanceId = User::where('divisi', 'Finance dan Gudang')->where('is_kepala_divisi', true)->first()?->id;

        // Cek jika ini adalah persetujuan final (oleh Kepala Finance)
        if ($pengajuanDana->status_atasan === 'disetujui' && $user->id === $kepalaFinanceId) {
            $updateData['status'] = 'disetujui';
        }

        $pengajuanDana->update($updateData);

        return redirect()->route('pengajuan_dana.show', $pengajuanDana)->with('success', 'Pengajuan dana berhasil disetujui!');
    }

    public function reject(Request $request, PengajuanDana $pengajuanDana)
    {
        // Otorisasi juga menggunakan Policy
        $this->authorize('approve', $pengajuanDana);
        
        $user = Auth::user();
        $updateData = ['status' => 'ditolak'];

        // Cek apakah user adalah Kepala Divisi dari divisi yang mengajukan
        if ($user->is_kepala_divisi && $user->divisi === $pengajuanDana->divisi) {
             $updateData['status_atasan'] = 'ditolak';
             $updateData['catatan_atasan'] = $request->catatan_penolakan ?? 'Ditolak';
        } else { // Jika bukan Kepala Divisi (berarti Kepala Finance)
             $updateData['status_finance'] = 'ditolak';
             $updateData['catatan_finance'] = $request->catatan_penolakan ?? 'Ditolak';
        }

        $pengajuanDana->update($updateData);

        return redirect()->route('pengajuan_dana.show', $pengajuanDana)->with('success', 'Pengajuan dana berhasil ditolak!');
    }
}