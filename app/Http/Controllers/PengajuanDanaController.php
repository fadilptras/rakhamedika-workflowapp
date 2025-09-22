<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanDana;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

    /**
     * ===============================================
     * PERUBAHAN UTAMA ADA DI FUNGSI INI
     * ===============================================
     */
    public function show(PengajuanDana $pengajuanDana)
    {
        $user = Auth::user();
        $pemohon = $pengajuanDana->user; // Mengambil data user yang mengajukan dana

        // 1. Cek apakah user yang login adalah pemilik pengajuan
        $isOwner = $user->id === $pemohon->id;

        // 2. Cari siapa Kepala Divisi dari si pemohon
        $kepalaDivisi = User::where('divisi', $pemohon->divisi)
                              ->where('is_kepala_divisi', true)
                              ->first();
        
        // 3. Cek apakah user yang login adalah Kepala Divisi yang dituju
        $isKepalaDivisi = $kepalaDivisi && $user->id === $kepalaDivisi->id;

        // 4. Daftar jabatan lain yang boleh melihat (Finance, HRD, Direktur)
        $hasAllowedJabatan = in_array($user->jabatan, ['Kepala Finance dan Gudang', 'HRD', 'Direktur']);

        // 5. Jika user BUKAN salah satu dari ketiga kondisi di atas, tolak akses
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
        if ($request->has('rincian_deskripsi')) {
            foreach ($request->input('rincian_deskripsi') as $key => $deskripsi) {
                $rincian[] = [
                    'deskripsi' => $deskripsi,
                    'jumlah' => $request->input('rincian_jumlah')[$key],
                ];
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

        // Cari Kepala Divisi pemohon
        $kepalaDivisi = User::where('divisi', $user->divisi)
                              ->where('is_kepala_divisi', true)
                              ->first();
        
        // Cari HRD untuk ditembuskan (CC)
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
        $user = Auth::user();
        $userJabatan = strtolower($user->jabatan);

        if (!$this->canApprove($user, $pengajuanDana)) {
             abort(403, 'Anda tidak memiliki hak akses untuk menyetujui pengajuan dana.');
        }

        $statusField = '';
        $catatanField = '';

        if ($user->is_kepala_divisi) {
             $statusField = 'status_atasan';
             $catatanField = 'catatan_atasan';
        } elseif ($userJabatan == 'kepala finance dan gudang') {
             $statusField = 'status_hrd'; // Menggunakan kolom status_hrd untuk finance
             $catatanField = 'catatan_hrd';
        }

        if ($statusField) {
            $pengajuanDana->update([
                $statusField => 'disetujui',
                $catatanField => $request->catatan_persetujuan ?? 'Disetujui',
            ]);
        }

        // Logika Notifikasi Berjenjang
        if ($user->is_kepala_divisi) {
            $kepalaFinance = User::where('jabatan', 'Kepala Finance dan Gudang')->first();
            if ($kepalaFinance) {
                Notification::send($kepalaFinance, new PengajuanDanaNotification($pengajuanDana));
            }
        }
        
        // Cek apakah semua pihak sudah menyetujui
        if ($pengajuanDana->status_atasan === 'disetujui' && $pengajuanDana->status_hrd === 'disetujui') {
            $pengajuanDana->update(['status' => 'disetujui']);
        }

        return redirect()->route('pengajuan_dana.show', $pengajuanDana->id)->with('success', 'Pengajuan dana berhasil disetujui!');
    }

    public function reject(Request $request, PengajuanDana $pengajuanDana)
    {
        $user = Auth::user();
        $userJabatan = strtolower($user->jabatan);
        
        if (!$this->canApprove($user, $pengajuanDana)) {
             abort(403, 'Anda tidak memiliki hak akses untuk menolak pengajuan dana.');
        }
        
        $statusField = '';
        $catatanField = '';

        if ($user->is_kepala_divisi) {
             $statusField = 'status_atasan';
             $catatanField = 'catatan_atasan';
        } elseif ($userJabatan == 'kepala finance dan gudang') {
             $statusField = 'status_hrd'; // Menggunakan kolom status_hrd untuk finance
             $catatanField = 'catatan_hrd';
        }

        if ($statusField) {
            $pengajuanDana->update([
                $statusField => 'ditolak',
                $catatanField => $request->catatan_penolakan ?? 'Ditolak',
                'status' => 'ditolak'
            ]);
        }

        return redirect()->route('pengajuan_dana.show', $pengajuanDana->id)->with('success', 'Pengajuan dana berhasil ditolak!');
    }

    // Helper function untuk otorisasi approve/reject
    private function canApprove(User $user, PengajuanDana $pengajuanDana): bool
    {
        $pemohon = $pengajuanDana->user;
        $kepalaDivisi = User::where('divisi', $pemohon->divisi)->where('is_kepala_divisi', true)->first();

        // Boleh jika user adalah Kepala Divisi yang dituju DAN statusnya masih menunggu
        if ($kepalaDivisi && $user->id === $kepalaDivisi->id && $pengajuanDana->status_atasan === 'menunggu') {
            return true;
        }

        // Boleh jika user adalah Kepala Finance DAN Kepala Divisi sudah approve
        if ($user->jabatan === 'Kepala Finance dan Gudang' && $pengajuanDana->status_atasan === 'disetujui' && $pengajuanDana->status_hrd === 'menunggu') {
            return true;
        }

        return false;
    }
}