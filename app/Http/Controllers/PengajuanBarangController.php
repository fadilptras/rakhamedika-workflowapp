<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanBarang;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PengajuanBarangNotification;
use Illuminate\Http\RedirectResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class PengajuanBarangController extends Controller
{
    /**
     * Menampilkan daftar pengajuan barang milik user.
     */
    public function index()
    {
        $title = 'Pengajuan Barang';
        $pengajuanBarangs = Auth::user()->pengajuanBarangs()->orderBy('created_at', 'desc')->get();
        return view('users.pengajuan-barang', compact('title', 'pengajuanBarangs'));
    }

    /**
     * Menyimpan pengajuan barang baru.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'judul_pengajuan' => 'required|string|max:255',
            'divisi' => 'required|string|max:255',
            'rincian_deskripsi.*' => 'required|string|max:1000',
            'rincian_jumlah.*' => 'required|integer|min:1',
            'rincian_satuan.*' => 'required|string',
            'file_pendukung.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        // Proses Rincian Barang
        $rincian = [];
        if (!empty($validatedData['rincian_deskripsi'])) {
            foreach ($validatedData['rincian_deskripsi'] as $key => $deskripsi) {
                $rincian[] = [
                    'deskripsi' => $deskripsi,
                    'jumlah' => $validatedData['rincian_jumlah'][$key],
                    'satuan' => $request->rincian_satuan[$key],
                ];
            }
        }

        // Proses Upload File
        $pathFiles = [];
        if ($request->hasFile('file_pendukung')) {
            foreach ($request->file('file_pendukung') as $file) {
                $pathFiles[] = $file->store('lampiran_barang', 'public');
            }
        }

        // Logic Status Awal
        $isKepalaDivisi = Auth::user()->is_kepala_divisi;

        $pengajuan = PengajuanBarang::create([
            'user_id' => Auth::id(),
            'judul_pengajuan' => $validatedData['judul_pengajuan'],
            'divisi' => $validatedData['divisi'],
            'rincian_barang' => $rincian,
            'lampiran' => $pathFiles,
            'status_atasan' => $isKepalaDivisi ? 'skipped' : 'menunggu',
            'status_direktur' => 'skipped',
            'status_gudang' => 'menunggu', // Sesuai kolom database baru
            'status' => 'diajukan',
        ]);

        // Kirim Notifikasi
        $user = Auth::user();
        if (!$user->is_kepala_divisi) {
            // Jika staf, notif ke Kepala Divisi
            $kepalaDivisi = User::where('divisi', $user->divisi)
                                ->where('is_kepala_divisi', true)
                                ->where('id', '!=', $user->id)
                                ->first();
            if ($kepalaDivisi) {
                $kepalaDivisi->notify(new PengajuanBarangNotification($pengajuan, 'baru'));
            }
        } else {
            // Jika Kepala Divisi, langsung notif ke Gudang
            // Pastikan Anda memiliki user dengan jabatan yang mengandung kata 'Gudang'
            $gudang = User::whereIn('divisi', ['Finance & Gudang', 'Finance dan Gudang'])
                        ->where('jabatan', 'like', '%Gudang%')
                        ->first();
            if ($gudang) {
                $gudang->notify(new PengajuanBarangNotification($pengajuan, 'baru'));
            }
        }

        return redirect()->route('pengajuan_barang.index')->with('success', 'Pengajuan barang berhasil dikirim!');
    }

    /**
     * Menampilkan detail pengajuan barang.
     */
    public function show(PengajuanBarang $pengajuanBarang)
    {
        $user = Auth::user();

        // Logic Akses: Pemilik, Admin, Atasan Divisi, atau Orang Gudang
        $isOwner = $user->id === $pengajuanBarang->user_id;
        $isAdmin = $user->role === 'admin';

        // Cek Atasan: User adalah Kepala Divisi DAN satu divisi dengan pengajuan
        $isAtasan = $user->is_kepala_divisi && ($user->divisi === $pengajuanBarang->divisi);

        // Cek Gudang: Menggunakan 'like' agar lebih fleksibel ('Gudang', 'Admin Gudang', 'Staf Gudang')
        $isGudang = str_contains(strtolower($user->jabatan ?? ''), 'gudang');

        if (! $isOwner && ! $isAdmin && ! $isAtasan && ! $isGudang) {
            abort(403, 'Anda tidak memiliki akses ke pengajuan ini.');
        }

        return view('users.detail-pengajuan-barang', [
            'title' => 'Detail Pengajuan Barang',
            'pengajuanBarang' => $pengajuanBarang,
        ]);
    }

    /**
     * Download PDF
     */
    public function download(PengajuanBarang $pengajuanBarang)
    {
        // Pastikan view PDF sudah diperbarui sesuai kode yang saya berikan sebelumnya
        $pdf = Pdf::loadView('pdf.pengajuan-barang', compact('pengajuanBarang'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->download('Form-Pengajuan-Barang-' . $pengajuanBarang->id . '.pdf');
    }

    /**
     * Approval Logic (DIPERBARUI)
     */
    public function approve(Request $request, PengajuanBarang $pengajuanBarang): RedirectResponse
    {
        $pemohon = $pengajuanBarang->user;
        $updateData = [];
        $tipeNotifikasiUntukPemohon = '';

        // Cek Tahap Approval
        if ($pengajuanBarang->status_atasan === 'menunggu') {
            // --- TAHAP 1: Approval Atasan ---
            $updateData['status_atasan'] = 'disetujui';
            $updateData['catatan_atasan'] = $request->catatan_persetujuan ?? null;
            
            // SIMPAN DATA PENYETUJU (Penting untuk PDF)
            $updateData['atasan_id'] = Auth::id(); 
            $updateData['atasan_approved_at'] = now();
            
            $updateData['status'] = 'diproses';
            $tipeNotifikasiUntukPemohon = 'disetujui_atasan';

            // Notifikasi ke Gudang setelah atasan setuju
            $gudang = User::whereIn('divisi', ['Finance & Gudang', 'Finance dan Gudang'])
                        ->where('jabatan', 'like', '%Gudang%')
                        ->first();
            if ($gudang) {
                $gudang->notify(new PengajuanBarangNotification($pengajuanBarang, 'baru'));
            }

        } else {
            // --- TAHAP 2: Approval Gudang ---
            $updateData['status_gudang'] = 'disetujui';
            $updateData['catatan_gudang'] = $request->catatan_persetujuan ?? null;
            
            // SIMPAN DATA PENYETUJU (Penting untuk PDF)
            $updateData['gudang_id'] = Auth::id();
            $updateData['gudang_approved_at'] = now();
            
            $updateData['status'] = 'selesai';
            $tipeNotifikasiUntukPemohon = 'disetujui_gudang';
        }

        $pengajuanBarang->update($updateData);

        if ($pemohon) {
            $pemohon->notify(new PengajuanBarangNotification($pengajuanBarang, $tipeNotifikasiUntukPemohon));
        }

        return redirect()->route('pengajuan_barang.show', $pengajuanBarang)->with('success', 'Pengajuan barang berhasil disetujui!');
    }

    /**
     * Reject Logic
     */
    public function reject(Request $request, PengajuanBarang $pengajuanBarang): RedirectResponse
    {
        $updateData = ['status' => 'ditolak'];

        // Cek siapa yang menolak
        if ($pengajuanBarang->status_atasan === 'menunggu') {
            $updateData['status_atasan'] = 'ditolak';
            $updateData['catatan_atasan'] = $request->catatan_penolakan ?? null;
            // Opsional: $updateData['atasan_id'] = Auth::id();
        } else {
            $updateData['status_gudang'] = 'ditolak';
            $updateData['catatan_gudang'] = $request->catatan_penolakan ?? null;
            // Opsional: $updateData['gudang_id'] = Auth::id();
        }

        $pengajuanBarang->update($updateData);

        $pengajuanBarang->user->notify(new PengajuanBarangNotification($pengajuanBarang, 'ditolak'));

        return redirect()->route('pengajuan_barang.show', $pengajuanBarang)->with('success', 'Pengajuan barang ditolak.');
    }

    /**
     * Cancel Logic
     */
    public function cancel(PengajuanBarang $pengajuanBarang): RedirectResponse
    {
        if (Auth::id() !== $pengajuanBarang->user_id) {
            abort(403);
        }

        $pengajuanBarang->update([
            'status' => 'dibatalkan',
            'status_atasan' => 'dibatalkan',
            'status_gudang' => 'dibatalkan', 
        ]);

        return redirect()->route('pengajuan_barang.show', $pengajuanBarang)->with('success', 'Pengajuan berhasil dibatalkan.');
    }
}