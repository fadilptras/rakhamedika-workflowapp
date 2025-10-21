<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanDana;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Notifications\PengajuanDanaNotification;
use Illuminate\Support\Facades\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon; // Pastikan Carbon di-import

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
        $this->authorize('view', $pengajuanDana);

        // Lakukan Eager Loading untuk memuat relasi approver
        $pengajuanDana->load(['user', 'atasanApprover', 'direkturApprover', 'financeApprover']);

        return view('users.detail-pengajuan-dana', [
            'title' => 'Detail Pengajuan Dana',
            'pengajuanDana' => $pengajuanDana,
        ]);
    }
    
    public function store(Request $request)
    {
        // ... (Tidak ada perubahan di method ini)
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
            'file_pendukung.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);
        $rincian = [];
        if (!empty($validatedData['rincian_deskripsi'])) {
            foreach ($validatedData['rincian_deskripsi'] as $key => $deskripsi) {
                $rincian[] = ['deskripsi' => $deskripsi, 'jumlah' => $validatedData['rincian_jumlah'][$key]];
            }
        }
        $pathFiles = []; // Gunakan array untuk menampung path
        if ($request->hasFile('file_pendukung')) {
            foreach ($request->file('file_pendukung') as $file) {
                // Simpan setiap file dan tambahkan path-nya ke array
                $pathFiles[] = $file->store('lampiran_dana', 'public');
            }
        }
        $isKepalaDivisi = Auth::user()->is_kepala_divisi;
        $pengajuanDana = PengajuanDana::create([
            'user_id' => Auth::id(),
            'judul_pengajuan' => $validatedData['judul_pengajuan'],
            'divisi' => $validatedData['divisi'],
            'nama_bank' => $validatedData['nama_bank'] === 'other' ? $validatedData['nama_bank_lainnya'] : $validatedData['nama_bank'],
            'no_rekening' => $validatedData['no_rekening'],
            'total_dana' => $validatedData['jumlah_dana_total'],
            'rincian_dana' => $rincian,
            'lampiran' => $pathFiles,
            'status_atasan' => $isKepalaDivisi ? 'skipped' : 'menunggu',
            'status_direktur' => $isKepalaDivisi ? 'menunggu' : 'skipped',
        ]);
        $user = Auth::user();
        $atasan = null;
        if ($user->is_kepala_divisi) {
            $atasan = User::where('jabatan', 'Direktur')->first();
        } else if ($user->divisi) {
        $atasan = User::where('divisi', $user->divisi)
                    ->where('is_kepala_divisi', true)
                    ->first();
        }
        if (!$atasan && $user->jabatan !== 'Direktur') {
            $atasan = User::where('jabatan', 'Direktur')->first();
        }

        if ($atasan) {
            Notification::send($atasan, new PengajuanDanaNotification($pengajuanDana, 'baru'));
        }
        $hrd = User::where('jabatan', 'HRD')->first();
        if ($hrd && (!$atasan || $atasan->id !== $hrd->id)) {
            Notification::send($hrd, new PengajuanDanaNotification($pengajuanDana, 'baru'));
        }
        
        return redirect()->route('pengajuan_dana.index')->with('success', 'Pengajuan dana berhasil dikirim!');
    }

    public function approve(Request $request, PengajuanDana $pengajuanDana)
    {
        $this->authorize('approve', $pengajuanDana);
        
        $pemohon = $pengajuanDana->user;
        $updateData = [];
        $tipeNotifikasiUntukPemohon = '';

        if ($pengajuanDana->status_atasan === 'menunggu' || $pengajuanDana->status_direktur === 'menunggu') {
            if ($pemohon->is_kepala_divisi) {
                $updateData['status_direktur'] = 'disetujui';
                $updateData['catatan_direktur'] = $request->catatan_persetujuan;
                $updateData['direktur_id'] = Auth::id();
                $updateData['direktur_approved_at'] = Carbon::now(); // TAMBAHAN: Simpan tanggal approve
            } else {
                $updateData['status_atasan'] = 'disetujui';
                $updateData['catatan_atasan'] = $request->catatan_persetujuan;
                $updateData['atasan_id'] = Auth::id();
                $updateData['atasan_approved_at'] = Carbon::now(); // TAMBAHAN: Simpan tanggal approve
            }
            $updateData['status'] = 'diproses'; 
            $tipeNotifikasiUntukPemohon = 'disetujui_atasan';

            $kepalaFinance = User::where('divisi', 'Finance dan Gudang')->where('is_kepala_divisi', true)->first();
            if ($kepalaFinance) {
                Notification::send($kepalaFinance, new PengajuanDanaNotification($pengajuanDana, 'baru'));
            }
        }
        else {
            $updateData['status_finance'] = 'disetujui';
            $updateData['catatan_finance'] = $request->catatan_persetujuan;
            $updateData['finance_id'] = Auth::id();
            $updateData['finance_approved_at'] = Carbon::now(); // TAMBAHAN: Simpan tanggal approve
            $updateData['status'] = 'disetujui';
            $tipeNotifikasiUntukPemohon = 'disetujui_finance';
        }

        $pengajuanDana->update($updateData);

        Notification::send($pemohon, new PengajuanDanaNotification($pengajuanDana, $tipeNotifikasiUntukPemohon));
        
        return redirect()->route('pengajuan_dana.show', $pengajuanDana)->with('success', 'Pengajuan dana berhasil disetujui!');
    }

    public function reject(Request $request, PengajuanDana $pengajuanDana)
    {
        $this->authorize('approve', $pengajuanDana);
        $updateData = ['status' => 'ditolak'];
        if ($pengajuanDana->status_atasan === 'menunggu' || $pengajuanDana->status_direktur === 'menunggu') {
             if ($pengajuanDana->user->is_kepala_divisi) {
                $updateData['status_direktur'] = 'ditolak';
                $updateData['catatan_direktur'] = $request->catatan_penolakan;
                $updateData['direktur_id'] = Auth::id();
                $updateData['direktur_approved_at'] = Carbon::now(); // TAMBAHAN: Simpan tanggal reject
            } else {
                $updateData['status_atasan'] = 'ditolak';
                $updateData['catatan_atasan'] = $request->catatan_penolakan;
                $updateData['atasan_id'] = Auth::id();
                $updateData['atasan_approved_at'] = Carbon::now(); // TAMBAHAN: Simpan tanggal reject
            }
        } else {
            $updateData['status_finance'] = 'ditolak';
            $updateData['catatan_finance'] = $request->catatan_penolakan;
            $updateData['finance_id'] = Auth::id();
            $updateData['finance_approved_at'] = Carbon::now(); // TAMBAHAN: Simpan tanggal reject
        }
        $pengajuanDana->update($updateData);

        Notification::send($pengajuanDana->user, new PengajuanDanaNotification($pengajuanDana, 'ditolak'));

        return redirect()->route('pengajuan_dana.show', $pengajuanDana)->with('success', 'Pengajuan dana berhasil ditolak!');
    }

    // ... (Tidak ada perubahan di method uploadBuktiTransfer, uploadFinalInvoice, dan cancel)
    public function uploadBuktiTransfer(Request $request, PengajuanDana $pengajuanDana)
    {
        $this->authorize('uploadBuktiTransfer', $pengajuanDana); 
        $request->validate([
            'bukti_transfer' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);
        $path = $request->file('bukti_transfer')->store('bukti_transfer', 'public');
        $pengajuanDana->update(['bukti_transfer' => $path]);

        Notification::send($pengajuanDana->user, new PengajuanDanaNotification($pengajuanDana, 'bukti_transfer'));
        
        return redirect()->route('pengajuan_dana.show', $pengajuanDana)->with('success', 'Bukti transfer berhasil diunggah!');
    }

    public function uploadFinalInvoice(Request $request, PengajuanDana $pengajuanDana)
    {
        $this->authorize('uploadFinalInvoice', $pengajuanDana);
        $request->validate([
            'invoice' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);
        $path = $request->file('invoice')->store('invoices', 'public');
        $pengajuanDana->update(['invoice' => $path]);
        return redirect()->route('pengajuan_dana.show', $pengajuanDana)->with('success', 'Invoice berhasil diunggah!');
    }

    public function cancel(PengajuanDana $pengajuanDana)
    {
        $this->authorize('cancel', $pengajuanDana);
        $pengajuanDana->update(['status' => 'dibatalkan']);
        $pemohon = $pengajuanDana->user;
        $atasan = null;
        if ($pemohon->is_kepala_divisi) {
            $atasan = User::where('jabatan', 'Direktur')->first();
        } else if ($pemohon->divisi) {
            $atasan = User::where('divisi', $pemohon->divisi)->where('is_kepala_divisi', true)->first();
        }
        if (!$atasan && $pemohon->jabatan !== 'Direktur') {
            $atasan = User::where('jabatan', 'Direktur')->first();
        }
        $kepalaFinance = User::where('divisi', 'Finance dan Gudang')->where('is_kepala_divisi', true)->first();
        if ($atasan) {
            Notification::send($atasan, new PengajuanDanaNotification($pengajuanDana, 'dibatalkan'));
        }
        if ($kepalaFinance && (!$atasan || $atasan->id !== $kepalaFinance->id)) {
            Notification::send($kepalaFinance, new PengajuanDanaNotification($pengajuanDana, 'dibatalkan'));
        }
        return redirect()->route('pengajuan_dana.index')->with('success', 'Pengajuan dana telah berhasil dibatalkan.');
    }

    public function downloadPDF(PengajuanDana $pengajuanDana)
    {
        // Pastikan user yang mengakses berhak melihat data ini
        $this->authorize('view', $pengajuanDana);

        // Load semua relasi yang dibutuhkan agar datanya muncul di PDF
        $pengajuanDana->load(['user', 'atasanApprover', 'direkturApprover', 'financeApprover']);

        // Data dikirim ke view PDF
        $pdf = PDF::loadView('users.pdf_pengajuan_dana', compact('pengajuanDana'));

        // Buat nama file yang dinamis
        $namaJudul = \Illuminate\Support\Str::slug($pengajuanDana->judul_pengajuan, '-');
        $filename = "pengajuan-dana-{$pengajuanDana->id}-{$namaJudul}.pdf";

        // Tawarkan file untuk diunduh oleh browser
        return $pdf->download($filename);
    }
}