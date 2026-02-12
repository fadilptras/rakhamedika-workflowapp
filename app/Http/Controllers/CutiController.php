<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Cuti;
use App\Models\Holiday;
use App\Models\User;
use App\Notifications\CutiNotification;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CutiController extends Controller
{
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

        $cutiTerpakai = $this->hitungCutiTerpakai($cutiDisetujui);
        $terpakaiTahunan = $cutiTerpakai['tahunan'] ?? 0;
        $sisaCuti = $totalCuti - $terpakaiTahunan;

        $cutiRequests = Cuti::where('user_id', $user->id)
            ->whereYear('created_at', $tahunIni)
            ->latest()
            ->get();

        return view('users.cuti', compact('title', 'sisaCuti', 'totalCuti', 'terpakaiTahunan', 'cutiRequests'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'jenis_cuti' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Logika Approval Otomatis jika Atasan tidak ada (Skipped)
        $statusApp1 = $user->approver_cuti_1_id ? 'menunggu' : 'skipped';
        $statusApp2 = $user->approver_cuti_2_id ? 'menunggu' : 'skipped';

        $statusGlobal = 'diajukan';
        if ($statusApp1 === 'skipped' && $statusApp2 === 'skipped') {
            $statusGlobal = 'disetujui';
        }

        $lampiranPath = $request->file('lampiran') ? $request->file('lampiran')->store('lampiran_cuti', 'public') : null;

        $cuti = Cuti::create([
            'user_id' => $user->id,
            'jenis_cuti' => $request->jenis_cuti,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alasan' => $request->alasan,
            'lampiran' => $lampiranPath,
            'status' => $statusGlobal,
            'status_approver_1' => $statusApp1,
            'status_approver_2' => $statusApp2,
        ]);

        if ($statusGlobal === 'disetujui') {
            $this->syncAbsensiCuti($cuti);
            return redirect()->route('cuti.create')->with('success', 'Cuti otomatis disetujui karena tidak ada atasan.');
        }

        // Jalankan logic notifikasi yang Anda minta
        $this->notifyNextApproverOnCreate($cuti);

        return redirect()->route('cuti.create')->with('success', 'Pengajuan cuti berhasil dikirim.');
    }

    public function show(Cuti $cuti)
    {
        $user = Auth::user();
        $cuti->load('user');
        $pemohon = $cuti->user;

        // Cek Akses: Pemilik, Admin, atau Atasan terkait
        $isOwner = $user->id === $cuti->user_id;
        $isAdmin = $user->role === 'admin';
        $isApprover1 = ($pemohon->approver_cuti_1_id == $user->id);
        $isApprover2 = ($pemohon->approver_cuti_2_id == $user->id);

        if (!$isOwner && !$isAdmin && !$isApprover1 && !$isApprover2) {
            abort(403, 'Anda tidak memiliki akses ke detail pengajuan ini.');
        }

        $approver1 = $pemohon->approver_cuti_1_id ? User::find($pemohon->approver_cuti_1_id) : null;
        $approver2 = $pemohon->approver_cuti_2_id ? User::find($pemohon->approver_cuti_2_id) : null;

        return view('users.detail-cuti', [
            'title' => 'Detail Cuti',
            'cuti' => $cuti,
            'approver1' => $approver1,
            'approver2' => $approver2,
        ]);
    }

    public function updateStatus(Request $request, Cuti $cuti)
    {
        $user = Auth::user();
        $pemohon = $cuti->user;

        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan_approval' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request, $cuti, $user, $pemohon) {
            $isApprover1 = ($pemohon->approver_cuti_1_id == $user->id);
            $isApprover2 = ($pemohon->approver_cuti_2_id == $user->id);

            if ($isApprover1) {
                $cuti->status_approver_1 = $request->status;
                $cuti->tanggal_approve_1 = now();
                
                if ($request->status === 'ditolak') {
                    $cuti->status = 'ditolak';
                } elseif ($cuti->status_approver_2 === 'skipped') {
                    $cuti->status = 'disetujui';
                    $this->syncAbsensiCuti($cuti);
                } else {
                    // Berlanjut ke Approver 2
                    $this->notifyApprover2($cuti);
                }
            } elseif ($isApprover2) {
                $cuti->status_approver_2 = $request->status;
                $cuti->tanggal_approve_2 = now();
                
                if ($request->status === 'ditolak') {
                    $cuti->status = 'ditolak';
                } else {
                    $cuti->status = 'disetujui';
                    $this->syncAbsensiCuti($cuti);
                }
            }

            $cuti->catatan_approval = $request->catatan_approval;
            $cuti->save();

            // Kirim notifikasi balik ke pemohon
            try {
                $pemohon->notify(new CutiNotification($cuti, $request->status));
            } catch (\Exception $e) {
                Log::error("Gagal kirim notif status: " . $e->getMessage());
            }
        });

        return redirect()->back()->with('success', 'Status cuti berhasil diperbarui.');
    }

    private function notifyNextApproverOnCreate(Cuti $cuti): void
    {
        try {
            $pemohon = $cuti->user;
            // Kirim ke Approver 1 jika ada
            if ($pemohon->approver_cuti_1_id) {
                $approver1 = User::find($pemohon->approver_cuti_1_id);
                if ($approver1) {
                    $approver1->notify(new CutiNotification($cuti, 'baru'));
                }
            } 
            // Jika Approver 1 tidak ada tapi Approver 2 ada, langsung ke Approver 2
            elseif ($pemohon->approver_cuti_2_id) {
                $approver2 = User::find($pemohon->approver_cuti_2_id);
                if ($approver2) {
                    $approver2->notify(new CutiNotification($cuti, 'baru'));
                }
            }
        } catch (\Exception $e) {
            Log::error("Gagal kirim notif buat cuti: " . $e->getMessage());
        }
    }

    private function notifyApprover2(Cuti $cuti): void
    {
        $pemohon = $cuti->user;
        if ($pemohon->approver_cuti_2_id) {
            $approver2 = User::find($pemohon->approver_cuti_2_id);
            if ($approver2) {
                $approver2->notify(new CutiNotification($cuti, 'baru'));
            }
        }
    }

    private function hitungCutiTerpakai($cutiCollection): array
    {
        $cutiTerpakai = [];
        $holidays = Holiday::pluck('tanggal')->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))->toArray();

        foreach ($cutiCollection as $cuti) {
            $days = 0;
            $period = CarbonPeriod::create($cuti->tanggal_mulai, $cuti->tanggal_selesai);
            foreach ($period as $date) {
                if (!$date->isSunday() && !in_array($date->format('Y-m-d'), $holidays)) {
                    $days++;
                }
            }
            $jenis = strtolower($cuti->jenis_cuti);
            $cutiTerpakai[$jenis] = ($cutiTerpakai[$jenis] ?? 0) + $days;
        }
        return $cutiTerpakai;
    }

    private function syncAbsensiCuti(Cuti $cuti): void
    {
        $holidays = Holiday::pluck('tanggal')->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))->toArray();
        $period = CarbonPeriod::create($cuti->tanggal_mulai, $cuti->tanggal_selesai);

        foreach ($period as $date) {
            if ($date->isSunday() || in_array($date->format('Y-m-d'), $holidays)) continue;

            Absensi::updateOrCreate(
                ['user_id' => $cuti->user_id, 'tanggal' => $date->format('Y-m-d')],
                [
                    'status' => 'cuti', 
                    'keterangan' => 'Cuti: ' . $cuti->jenis_cuti,
                    // TAMBAHKAN INI agar tidak error 1364
                    'jam_masuk' => '00:00:00', 
                    'jam_keluar' => '00:00:00'
                ]
            );
        }
    }

    /**
     * Membatalkan Pengajuan Cuti
     */
    public function cancel(Cuti $cuti)
    {
        if (Auth::id() !== $cuti->user_id) {
            abort(403);
        }

        // Jika sudah disetujui sebelumnya, hapus sinkronisasi absensinya
        if ($cuti->status === 'disetujui') {
            \App\Models\Absensi::where('user_id', $cuti->user_id)
                ->where('status', 'cuti')
                ->whereBetween('tanggal', [$cuti->tanggal_mulai, $cuti->tanggal_selesai])
                ->delete();
        }

        $cuti->update([
            'status' => 'dibatalkan',
            'status_approver_1' => 'dibatalkan',
            'status_approver_2' => 'dibatalkan'
        ]);

        return redirect()->route('cuti.create')->with('success', 'Pengajuan cuti telah berhasil dibatalkan.');
    }

    /**
     * Download Formulir Cuti dalam bentuk PDF
     */
    public function download(Cuti $cuti)
    {
        $user = Auth::user();
        $pemohon = $cuti->user;
        
        // Cek Akses (Pemilik, Admin, atau Approver)
        $isOwner = $user->id === $cuti->user_id;
        $isAdmin = $user->role === 'admin';
        $isApprover = ($pemohon->approver_cuti_1_id === $user->id || $pemohon->approver_cuti_2_id === $user->id);

        if (!$isOwner && !$isAdmin && !$isApprover) {
            abort(403);
        }
        
        $approver1 = $pemohon->approver_cuti_1_id ? User::find($pemohon->approver_cuti_1_id) : null;
        $approver2 = $pemohon->approver_cuti_2_id ? User::find($pemohon->approver_cuti_2_id) : null;
        
        $tahunCuti = \Carbon\Carbon::parse($cuti->tanggal_mulai)->year;

        $cutiDisetujui = Cuti::where('user_id', $pemohon->id)
            ->where('status', 'disetujui')
            ->whereYear('tanggal_mulai', $tahunCuti)
            ->get();

        $cutiTerpakai = $this->hitungCutiTerpakai($cutiDisetujui);
        $terpakaiCount = $cutiTerpakai['tahunan'] ?? 0;
        $sisaCuti = ($pemohon->jatah_cuti ?? 12) - $terpakaiCount;

        // Pastikan package barryvdh/laravel-dompdf sudah terinstall
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.cuti', [
            'cuti' => $cuti,
            'approver1' => $approver1, 
            'approver2' => $approver2, 
            'sisaCuti' => $sisaCuti,
            'user' => $pemohon
        ]);
        
        $pdf->setPaper('a4', 'portrait');
        return $pdf->download('Formulir-Cuti-' . $pemohon->name . '.pdf');
    }
}