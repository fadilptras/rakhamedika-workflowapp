<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\User;
use App\Notifications\CutiNotification;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class CutiController extends Controller
{
    /**
     * Menampilkan form pembuatan cuti dan riwayat cuti user.
     */
    public function create()
    {
        $user = Auth::user();
        $totalCuti = $user->jatah_cuti ?? 12;
        $tahunIni = Carbon::now()->year;
        $title = 'Pengajuan Cuti';

        $cutiDisetujui = Cuti::where('user_id', $user->id)
            ->where('status', 'disetujui')
            ->whereYear('tanggal_mulai', $tahunIni)
            ->get();

        $terpakaiTahunan = $cutiDisetujui->sum('total_hari');
        $sisaCuti = $totalCuti - $terpakaiTahunan;

        $cutiRequests = Cuti::where('user_id', $user->id)
            ->whereYear('created_at', $tahunIni)
            ->latest()
            ->get();

        return view('users.cuti', compact('title', 'sisaCuti', 'terpakaiTahunan', 'cutiRequests'));
    }

    /**
     * Menyimpan data pengajuan cuti baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_cuti' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string|max:500',
            'lampiran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $user = Auth::user();
        
        // Logika Menghitung Hari Kerja (Opsional, sesuaikan dengan helper Anda)
        $totalHari = Carbon::parse($request->tanggal_mulai)->diffInDays(Carbon::parse($request->tanggal_selesai)) + 1;

        $path = $request->hasFile('lampiran') ? $request->file('lampiran')->store('lampiran_cuti', 'public') : null;

        $cuti = Cuti::create([
            'user_id' => $user->id,
            'jenis_cuti' => $request->jenis_cuti,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'total_hari' => $totalHari,
            'alasan' => $request->alasan,
            'lampiran' => $path,
            'status' => 'diajukan',
            'approver_cuti_1_id' => $user->approver_cuti_1_id,
            'approver_cuti_2_id' => $user->approver_cuti_2_id,
            'approver_cuti_3_id' => $user->approver_cuti_3_id, // Finalisasi
            'status_approver_1' => $user->approver_cuti_1_id ? 'menunggu' : 'skipped',
            'status_approver_2' => $user->approver_cuti_2_id ? 'menunggu' : 'skipped',
            'status_approver_3' => $user->approver_cuti_3_id ? 'menunggu' : 'skipped',
        ]);

        // Kirim Notifikasi ke Approver 1
        if ($user->approverCuti1) {
            Notification::send($user->approverCuti1, new CutiNotification($cuti, 'baru'));
        }

        return redirect()->route('cuti.create')->with('success', 'Pengajuan cuti berhasil dikirim.');
    }

    /**
     * Menampilkan detail cuti (Termasuk akses untuk Approver 3).
     */
    public function show($id)
    {
        $user = Auth::user();
        // Eager load semua relasi agar tidak ada data kosong di view
        $cuti = Cuti::with(['user', 'approver1', 'approver2', 'approver3'])->findOrFail($id);
        
        $isOwner = $user->id === $cuti->user_id;
        $isAdmin = $user->role === 'admin';
        // Tambahkan pengecekan untuk Approver 3
        $isApprover = in_array($user->id, [
            $cuti->approver_cuti_1_id, 
            $cuti->approver_cuti_2_id, 
            $cuti->approver_cuti_3_id
        ]);

        if (!$isOwner && !$isAdmin && !$isApprover) {
            abort(403, 'Anda tidak memiliki akses ke pengajuan ini.');
        }

        $title = 'Detail Pengajuan Cuti';
        
        // Logika Sisa Cuti Pemohon
        $tahunCuti = Carbon::parse($cuti->tanggal_mulai)->year;
        $cutiDisetujui = Cuti::where('user_id', $cuti->user_id)
            ->where('status', 'disetujui')
            ->whereYear('tanggal_mulai', $tahunCuti)
            ->get();
        
        $sisaCuti = ($cuti->user->jatah_cuti ?? 12) - $cutiDisetujui->sum('total_hari');

        return view('users.detail-cuti', compact('cuti', 'sisaCuti', 'title'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string|max:255'
        ]);

        // Eager load semua relasi agar tidak error saat kirim notif
        $cuti = Cuti::with(['user', 'approver1', 'approver2', 'approver3'])->findOrFail($id);
        $user = Auth::user();
        $statusInput = $request->status;

        // --- ALUR APPROVER 1 ---
        if ($user->id == $cuti->approver_cuti_1_id && $cuti->status_approver_1 == 'menunggu') {
            $cuti->update([
                'status_approver_1' => $statusInput,
                'catatan_approver_1' => $request->catatan,
            ]);

            if ($statusInput == 'disetujui') {
                // KIRIM NOTIF KE APPROVER 2
                if ($cuti->approver2) {
                    Notification::send($cuti->approver2, new CutiNotification($cuti, 'baru'));
                }
            } else {
                $cuti->update(['status' => 'ditolak']);
                Notification::send($cuti->user, new CutiNotification($cuti, 'ditolak'));
            }
        } 

        // --- ALUR APPROVER 2 (Hanya bisa jika App 1 sudah setuju) ---
        elseif ($user->id == $cuti->approver_cuti_2_id && $cuti->status_approver_1 == 'disetujui' && $cuti->status_approver_2 == 'menunggu') {
            $cuti->update([
                'status_approver_2' => $statusInput,
                'catatan_approver_2' => $request->catatan,
            ]);

            if ($statusInput == 'disetujui') {
                // KIRIM NOTIF KE APPROVER 3
                if ($cuti->approver3) {
                    Notification::send($cuti->approver3, new CutiNotification($cuti, 'baru'));
                }
            } else {
                $cuti->update(['status' => 'ditolak']);
                Notification::send($cuti->user, new CutiNotification($cuti, 'ditolak'));
            }
        }

        // --- ALUR APPROVER 3 / FINAL (Hanya bisa jika App 2 sudah setuju) ---
        elseif ($user->id == $cuti->approver_cuti_3_id && $cuti->status_approver_2 == 'disetujui' && $cuti->status_approver_3 == 'menunggu') {
            $cuti->update([
                'status_approver_3' => $statusInput,
                'catatan_approver_3' => $request->catatan,
                'status' => $statusInput // Update status utama pengajuan
            ]);

            // NOTIF FINAL KE PEMOHON
            Notification::send($cuti->user, new CutiNotification($cuti, $statusInput));
        } 
        
        else {
            return redirect()->back()->with('error', 'Otoritas tidak valid atau urutan persetujuan belum sampai ke Anda.');
        }

        return redirect()->back()->with('success', 'Status pengajuan berhasil diperbarui.');
    }
    /**
     * Download PDF Cuti (Eager Load Relasi).
     */
    public function downloadPdf($id)
    {
        $cuti = Cuti::with(['user', 'approver1', 'approver2', 'approver3'])->findOrFail($id);
        
        // Anda perlu menghitung sisa cuti di sini jika ingin menampilkannya di PDF
        $user = $cuti->user;
        $totalCuti = $user->jatah_cuti ?? 12;
        $terpakai = Cuti::where('user_id', $user->id)
            ->where('status', 'disetujui')
            ->whereYear('tanggal_mulai', \Carbon\Carbon::parse($cuti->tanggal_mulai)->year)
            ->sum('total_hari');
        $sisaCuti = $totalCuti - $terpakai;

        $pdf = Pdf::loadView('pdf.cuti', [
            'cuti' => $cuti,
            'sisaCuti' => $sisaCuti // Tambahkan ini agar tidak undefined
        ])->setPaper('a4', 'portrait');

        $fileName = 'Cuti_' . ($cuti->user->name ?? 'User') . '_' . $cuti->id . '.pdf';
        return $pdf->download($fileName);
    }

    public function cancel(Cuti $cuti)
    {
        if (Auth::id() !== $cuti->user_id) abort(403);
        if ($cuti->status !== 'diajukan') return redirect()->back()->with('error', 'Pengajuan sudah diproses, tidak bisa dibatalkan.');

        $cuti->status = 'dibatalkan';
        $cuti->save();

        return redirect()->back()->with('success', 'Pengajuan berhasil dibatalkan.');
    }
}