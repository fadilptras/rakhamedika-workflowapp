<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\User;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CutiNotification;
use Barryvdh\DomPDF\Facade\Pdf;

class CutiController extends Controller
{
    /**
     * Menampilkan Form Pengajuan Cuti
     */
    public function create()
    {
        $user = Auth::user();
        $totalCuti = $user->jatah_cuti ?? 12; // Default 12
        $tahunIni = Carbon::now()->year;

        $title = 'Pengajuan Cuti';

        // Hitung cuti disetujui tahun ini
        $cutiDisetujui = Cuti::where('user_id', $user->id)
            ->where('status', 'disetujui')
            ->whereYear('tanggal_mulai', $tahunIni)
            ->get();

        $cutiTerpakai = $this->hitungCutiTerpakai($cutiDisetujui);
        
        // Asumsi 'tahunan' adalah key yang konsisten. 
        // Jika di DB kamu tersimpan "Tahunan" (huruf besar), sesuaikan di sini.
        $terpakaiTahunan = $cutiTerpakai['tahunan'] ?? ($cutiTerpakai['Tahunan'] ?? 0); 
        $sisaCuti = $totalCuti - $terpakaiTahunan; 

        $cutiRequests = Cuti::where('user_id', $user->id)
            ->whereYear('created_at', $tahunIni)
            ->latest()
            ->get();

        return view('users.cuti', compact('sisaCuti', 'totalCuti', 'cutiRequests', 'title'));
    }
    
    /**
     * Menampilkan Detail Cuti
     */
    public function show(Cuti $cuti)
    {
        // [FIX] Menambahkan variabel title agar tidak error di view
        $title = 'Detail Pengajuan Cuti';
        
        // Validasi akses (User ybs, Admin, atau Atasan)
        $approverId = $cuti->approver_1_id ?? null;
        if (Auth::id() !== $cuti->user_id && Auth::user()->role !== 'admin' && Auth::id() !== $approverId) {
            // Cek logic approver lama jika approver_1_id null
            $approverLama = $this->getApprover($cuti->user);
            if (!$approverLama || Auth::id() !== $approverLama->id) {
                 // abort(403, 'Unauthorized action.'); // Uncomment jika ingin strict
            }
        }

        $approver = $this->getApprover($cuti->user);
        
        return view('users.detail-cuti', compact('cuti', 'approver', 'title'));
    }

    /**
     * Menyimpan Pengajuan Cuti (Gabungan Logic Lama & Notif Baru)
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validatedData = $request->validate([
            // Sesuaikan validasi jenis cuti dengan opsi di form kamu
            'jenis_cuti' => 'required|string', 
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string|max:2000',
            'lampiran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $user = Auth::user();
        
        // 2. [LOGIC LAMA] Cek Sisa Cuti
        // Pastikan jenis cuti Tahunan dicek kuotanya
        if (strtolower($validatedData['jenis_cuti']) == 'tahunan') {
            $cutiDisetujui = Cuti::where('user_id', $user->id)
                ->where('status', 'disetujui')
                ->whereYear('tanggal_mulai', now()->year)
                ->get();
            
            $terpakai = $this->hitungCutiTerpakai($cutiDisetujui);
            $terpakaiCount = $terpakai['tahunan'] ?? ($terpakai['Tahunan'] ?? 0);
            
            $sisaCuti = ($user->jatah_cuti ?? 12) - $terpakaiCount;
            
            $durasiCuti = Carbon::parse($validatedData['tanggal_mulai'])
                ->diffInDays(Carbon::parse($validatedData['tanggal_selesai'])) + 1;

            if ($durasiCuti > $sisaCuti) {
                return redirect()->back()->withInput()->withErrors(['tanggal_selesai' => 'Durasi cuti melebihi sisa cuti Anda (tersisa ' . $sisaCuti . ' hari).']);
            }
        }
        
        // 3. [LOGIC LAMA] Cek Tanggal Bertabrakan
        $isOverlapping = Cuti::where('user_id', $user->id)
            ->whereIn('status', ['diajukan', 'disetujui']) // Pastikan status sesuai DB ('diajukan' atau 'menunggu')
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

        // 4. [LOGIC LAMA] Cari Approver
        $approver = $this->getApprover($user);
        if (!$approver) {
            return redirect()->back()->withInput()->with('error', 'Gagal: Atasan tidak ditemukan. Hubungi admin.');
        }

        // 5. Upload File
        $pathLampiran = $request->hasFile('lampiran') ? $request->file('lampiran')->store('lampiran_cuti', 'public') : null;

        // 6. Simpan Data
        $cuti = Cuti::create([
            'user_id' => $user->id,
            'status' => 'diajukan', // Pastikan ini sesuai ENUM database kamu ('diajukan'/'menunggu')
            'jenis_cuti' => $validatedData['jenis_cuti'],
            'tanggal_mulai' => $validatedData['tanggal_mulai'],
            'tanggal_selesai' => $validatedData['tanggal_selesai'],
            'alasan' => $validatedData['alasan'],
            'lampiran' => $pathLampiran,
            'approver_1_id' => $approver->id, // Simpan ID approver agar mudah dilacak
        ]);
        
        // 7. [LOGIC BARU] Kirim Notifikasi dengan Try-Catch
        try {
            $approver->notify(new CutiNotification($cuti, 'baru'));
            Log::info("WA Cuti dikirim ke: " . $approver->name);

            // Opsional: Kirim ke HRD juga
            $hrd = User::where('jabatan', 'HRD')->first();
            if ($hrd && $hrd->id !== $approver->id) {
                $hrd->notify(new CutiNotification($cuti, 'baru'));
            }
        } catch (\Exception $e) {
            Log::error("Gagal kirim notif cuti: " . $e->getMessage());
        }

        return redirect()->route('cuti.create')->with('success', 'Pengajuan cuti berhasil dikirim.');
    }

    /**
     * Update Status (Approve/Reject)
     * [PENTING] Method ini wajib ada untuk tombol di detail-cuti.blade.php
     */
    public function updateStatus(Request $request, Cuti $cuti)
    {
        // $this->authorize('update', $cuti); // Nyalakan jika menggunakan Policy
        
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string',
        ]);

        $cuti->update([
            'status' => $request->status,
            'catatan_approval' => $request->catatan,
        ]);

        // Jika disetujui, update Tabel Absensi
        if ($request->status === 'disetujui') {
            $period = CarbonPeriod::create($cuti->tanggal_mulai, $cuti->tanggal_selesai);
            foreach ($period as $date) {
                Absensi::updateOrCreate(
                    ['user_id' => $cuti->user_id, 'tanggal' => $date->format('Y-m-d')],
                    ['status' => 'cuti', 'keterangan' => 'Cuti ' . $cuti->jenis_cuti, 'jam_masuk' => '00:00:00']
                );
            }
        }
        
        // Kirim Notifikasi Balikan ke User
        try {
            Notification::send($cuti->user, new CutiNotification($cuti, $request->status));
        } catch (\Exception $e) {
            Log::error("Gagal kirim notif status cuti: " . $e->getMessage());
        }

        return redirect()->route('cuti.show', $cuti)->with('success', 'Status pengajuan cuti berhasil diperbarui.');
    }

    /**
     * Membatalkan Cuti
     * [PENTING] Method ini wajib ada untuk tombol "Batalkan"
     */
    public function cancel(Cuti $cuti)
    {
        // $this->authorize('cancel', $cuti);

        // Hapus entri absensi jika cuti sebelumnya sudah disetujui
        if ($cuti->status === 'disetujui') {
            Absensi::where('user_id', $cuti->user_id)
                ->where('status', 'cuti')
                ->whereBetween('tanggal', [$cuti->tanggal_mulai, $cuti->tanggal_selesai])
                ->delete();
        }

        $cuti->update(['status' => 'dibatalkan']);

        // Notif Pembatalan ke Approver
        $approver = $this->getApprover($cuti->user);
        if ($approver) {
            try {
                $approver->notify(new CutiNotification($cuti, 'dibatalkan'));
            } catch (\Exception $e) {
                Log::error("Gagal notif batal: " . $e->getMessage());
            }
        }
        
        return redirect()->route('cuti.create')->with('success', 'Pengajuan cuti telah berhasil dibatalkan.');
    }

    /**
     * Download PDF
     */
    public function download(Cuti $cuti)
    {
        // Validasi
        if (Auth::id() !== $cuti->user_id && Auth::user()->role !== 'admin') {
             // abort(403);
        }
        
        $approver = $this->getApprover($cuti->user);
        $user = $cuti->user;
        $tahunIni = Carbon::now()->year;

        // Hitung ulang sisa cuti untuk ditampilkan di PDF
        $cutiDisetujui = Cuti::where('user_id', $user->id)
            ->where('status', 'disetujui')
            ->whereYear('tanggal_mulai', $tahunIni)
            ->get();

        $cutiTerpakai = $this->hitungCutiTerpakai($cutiDisetujui);
        $terpakaiCount = $cutiTerpakai['tahunan'] ?? ($cutiTerpakai['Tahunan'] ?? 0);
        $sisaCuti = ($user->jatah_cuti ?? 12) - $terpakaiCount;

        $pdf = Pdf::loadView('pdf.cuti', [
            'cuti' => $cuti,
            'approver' => $approver,
            'sisaCuti' => $sisaCuti,
            'user' => $user
        ]);
        
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download('Formulir-Cuti-' . $user->name . '.pdf');
    }

    /**
     * HELPER: Mencari Atasan secara berjenjang
     */
    private function getApprover(User $user): ?User
    {
        // Jika ada approver yang diset manual di DB (kolom approver_1_id atau atasan_id)
        if (!empty($user->approver_1_id)) {
            return User::find($user->approver_1_id);
        }

        // Logic Hierarchy Otomatis
        if ($user->jabatan === 'Direktur') {
            return null; // Direktur tidak punya atasan di sistem
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

        // Default ke Direktur jika tidak ada kepala divisi
        return User::where('jabatan', 'Direktur')->first();
    }

    /**
     * HELPER: Menghitung Statistik Cuti
     */
    private function hitungCutiTerpakai(object $cutiCollection): array
    {
        $cutiTerpakai = [];
        foreach ($cutiCollection as $cuti) {
            $durasi = Carbon::parse($cuti->tanggal_mulai)->diffInDays(Carbon::parse($cuti->tanggal_selesai)) + 1;
            
            // Normalize key (e.g. "Tahunan" vs "tahunan") if needed
            $jenis = $cuti->jenis_cuti; 
            
            if (array_key_exists($jenis, $cutiTerpakai)) {
                $cutiTerpakai[$jenis] += $durasi;
            } else {
                $cutiTerpakai[$jenis] = $durasi;
            }
        }
        return $cutiTerpakai;
    }
}