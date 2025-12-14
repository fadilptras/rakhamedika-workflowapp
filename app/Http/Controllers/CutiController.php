<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\User;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CutiNotification;
use App\Policies\CutiPolicy;
use Barryvdh\DomPDF\Facade\Pdf;

class CutiController extends Controller
{
    public function create()
    {
        $user = Auth::user();
        $totalCuti = $user->jatah_cuti;
        $tahunIni = Carbon::now()->year;

        $title = 'Pengajuan Cuti';

        $cutiDisetujui = Cuti::where('user_id', $user->id)
            ->where('status', 'disetujui')
            ->whereYear('tanggal_mulai', $tahunIni)
            ->get();

        $cutiTerpakai = $this->hitungCutiTerpakai($cutiDisetujui);
        $sisaCuti = $totalCuti - $cutiTerpakai['tahunan']; 

        $cutiRequests = Cuti::where('user_id', $user->id)
            ->whereYear('created_at', $tahunIni)
            ->latest()
            ->get();

        return view('users.cuti', compact('sisaCuti', 'totalCuti', 'cutiRequests', 'title'));
    }
    
    public function show(Cuti $cuti)
    {
        $this->authorize('view', $cuti);
        $approver = $this->getApprover($cuti->user);
        
        return view('users.detail-cuti', [
            'title' => 'Detail Pengajuan Cuti',
            'cuti' => $cuti,
            'approver' => $approver,
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'jenis_cuti' => 'required|in:tahunan',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string|max:2000',
            'lampiran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $user = Auth::user();
        
        $sisaCuti = $user->jatah_cuti - $this->hitungCutiTerpakai(
            Cuti::where('user_id', $user->id)->where('status', 'disetujui')->whereYear('tanggal_mulai', now()->year)->get()
        )['tahunan'];
        
        $durasiCuti = Carbon::parse($validatedData['tanggal_mulai'])->diffInDays(Carbon::parse($validatedData['tanggal_selesai'])) + 1;

        if ($durasiCuti > $sisaCuti) {
            return redirect()->back()->withInput()->withErrors(['tanggal_selesai' => 'Durasi cuti melebihi sisa cuti Anda (tersisa ' . $sisaCuti . ' hari).']);
        }
        
        $isOverlapping = Cuti::where('user_id', $user->id)
            ->whereIn('status', ['diajukan', 'disetujui'])
            ->where(function ($query) use ($validatedData) {
                $query->whereBetween('tanggal_mulai', [$validatedData['tanggal_mulai'], $validatedData['tanggal_selesai']])
                    ->orWhereBetween('tanggal_selesai', [$validatedData['tanggal_mulai'], $validatedData['tanggal_selesai']])
                    ->orWhere(function ($q) use ($validatedData) {
                        $q->where('tanggal_mulai', '<=', $validatedData['tanggal_mulai'])
                            ->where('tanggal_selesai', '>=', $validatedData['tanggal_selesai']);
                    });
            })->exists();

        if ($isOverlapping) {
            return redirect()->back()->withInput()->withErrors(['tanggal_mulai' => 'Anda sudah memiliki pengajuan cuti pada rentang tanggal tersebut.']);
        }

        $approver = $this->getApprover($user);
        if (!$approver) {
            return redirect()->back()->withInput()->with('error', 'Pengajuan gagal, atasan untuk persetujuan tidak ditemukan. Silakan hubungi admin.');
        }

        $pathLampiran = $request->hasFile('lampiran') ? $request->file('lampiran')->store('lampiran_cuti', 'public') : null;

        $cuti = Cuti::create([
            'user_id' => $user->id,
            'status' => 'diajukan',
            'jenis_cuti' => $validatedData['jenis_cuti'],
            'tanggal_mulai' => $validatedData['tanggal_mulai'],
            'tanggal_selesai' => $validatedData['tanggal_selesai'],
            'alasan' => $validatedData['alasan'],
            'lampiran' => $pathLampiran,
        ]);
        
        $approver->notify(new CutiNotification($cuti, 'baru'));
        $hrd = User::where('jabatan', 'HRD')->first();
        if ($hrd && $hrd->id !== $approver->id) {
            $hrd->notify(new CutiNotification($cuti, 'baru'));
        }

        return redirect()->route('cuti.create')->with('success', 'Pengajuan cuti Anda telah berhasil dikirim.');
    }

    public function updateStatus(Request $request, Cuti $cuti)
    {
        $this->authorize('update', $cuti);
        
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string',
        ]);

        $cuti->update([
            'status' => $request->status,
            'catatan_approval' => $request->catatan,
        ]);

        if ($request->status === 'disetujui') {
            $period = CarbonPeriod::create($cuti->tanggal_mulai, $cuti->tanggal_selesai);
            foreach ($period as $date) {
                Absensi::updateOrCreate(
                    ['user_id' => $cuti->user_id, 'tanggal' => $date->format('Y-m-d')],
                    ['status' => 'cuti', 'keterangan' => 'Cuti ' . $cuti->jenis_cuti, 'jam_masuk' => '00:00:00']
                );
            }
        }
        
        Notification::send($cuti->user, new CutiNotification($cuti, $request->status));

        return redirect()->route('cuti.show', $cuti)->with('success', 'Status pengajuan cuti berhasil diperbarui.');
    }

    public function cancel(Cuti $cuti)
    {
        $this->authorize('cancel', $cuti);

        // Hapus entri absensi jika cuti sebelumnya sudah disetujui
        if ($cuti->status === 'disetujui') {
            Absensi::where('user_id', $cuti->user_id)
                ->where('status', 'cuti')
                ->whereBetween('tanggal', [$cuti->tanggal_mulai, $cuti->tanggal_selesai])
                ->delete();
        }

        $cuti->update(['status' => 'dibatalkan']);

        // --- INILAH BAGIAN YANG DIPERBARUI ---
        // Cari approver dan kirim notifikasi pembatalan
        $approver = $this->getApprover($cuti->user);
        if ($approver) {
            $approver->notify(new CutiNotification($cuti, 'dibatalkan'));
        }
        
        // Kirim juga ke HRD jika HRD bukan approver utama
        $hrd = User::where('jabatan', 'HRD')->first();
        if ($hrd && (!$approver || $hrd->id !== $approver->id)) {
            $hrd->notify(new CutiNotification($cuti, 'dibatalkan'));
        }
        // --- AKHIR DARI PEMBARUAN ---

        return redirect()->route('cuti.create')->with('success', 'Pengajuan cuti telah berhasil dibatalkan.');
    }

    private function getApprover(User $user): ?User
    {
        if ($user->jabatan === 'Direktur') {
            return null;
        }

        if (str_starts_with($user->jabatan, 'Kepala')) {
            return User::where('jabatan', 'Direktur')->first();
        }
        
        if ($user->divisi) {
            $approver = User::where('divisi', $user->divisi)
                ->where('is_kepala_divisi', true)
                ->where('id', '!=', $user->id)
                ->first();
            if ($approver) {
                return $approver;
            }
        }

        return User::where('jabatan', 'Direktur')->first();
    }

    private function hitungCutiTerpakai(object $cutiCollection): array
    {
        $cutiTerpakai = ['tahunan' => 0];
        foreach ($cutiCollection as $cuti) {
            $durasi = Carbon::parse($cuti->tanggal_mulai)->diffInDays(Carbon::parse($cuti->tanggal_selesai)) + 1;
            if (array_key_exists($cuti->jenis_cuti, $cutiTerpakai)) {
                $cutiTerpakai[$cuti->jenis_cuti] += $durasi;
            }
        }
        return $cutiTerpakai;
    }

    public function download(Cuti $cuti)
    {
        $this->authorize('view', $cuti);
        
        // Ambil data approver untuk ditampilkan di PDF
        $approver = $this->getApprover($cuti->user);

        $pdf = Pdf::loadView('pdf.cuti', [
            'cuti' => $cuti,
            'approver' => $approver
        ]);
        
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download('Formulir-Cuti-' . $cuti->user->name . '-' . $cuti->created_at->format('dmY') . '.pdf');
    }
}