<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\User;
use App\Models\Absensi;
use App\Models\Holiday; 
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
        
        $totalCuti = $user->jatah_cuti ?? 12; 
        $tahunIni = Carbon::now()->year; 
        $title = 'Pengajuan Cuti';

        // Hitung cuti yang HANYA diambil di tahun INI
        $cutiDisetujui = Cuti::where('user_id', $user->id)
            ->where('status', 'disetujui')
            ->whereYear('tanggal_mulai', $tahunIni)
            ->get();

        $cutiTerpakai = $this->hitungCutiTerpakai($cutiDisetujui);
        $terpakaiTahunan = $cutiTerpakai['tahunan'] ?? ($cutiTerpakai['Tahunan'] ?? 0); 
        $sisaCuti = $totalCuti - $terpakaiTahunan; 

        // Riwayat Pengajuan
        $cutiRequests = Cuti::where('user_id', $user->id)
            ->whereYear('created_at', $tahunIni)
            ->latest()
            ->get();

        // [PERBAIKAN UTAMA]
        // Ambil data libur dan PAKSA formatnya jadi Y-m-d string
        // Agar JS bisa membacanya dengan tepat (menghindari format DateTime/Timestamp)
        $liburNasional = Holiday::whereYear('tanggal', '>=', $tahunIni)
            ->get()
            ->map(function ($h) {
                return Carbon::parse($h->tanggal)->format('Y-m-d');
            })
            ->values() // Reset keys agar jadi array murni
            ->toArray();

        return view('users.cuti', compact('sisaCuti', 'totalCuti', 'cutiRequests', 'title', 'liburNasional'));
    }
    
    /**
     * Menampilkan Detail Cuti
     */
    public function show(Cuti $cuti)
    {
        $title = 'Detail Pengajuan Cuti';
        
        $approverId = $cuti->approver_1_id ?? null;
        if (Auth::id() !== $cuti->user_id && Auth::user()->role !== 'admin' && Auth::id() !== $approverId) {
             // Authorization logic
        }

        $approver = $this->getApprover($cuti->user);
        
        return view('users.detail-cuti', compact('cuti', 'approver', 'title'));
    }

    /**
     * Menyimpan Pengajuan Cuti
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'jenis_cuti'      => 'required|string', 
            'tanggal_mulai'   => 'required|date|after_or_equal:today', 
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan'          => 'required|string|max:2000',
            'lampiran'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'tanggal_mulai.after_or_equal' => 'Tanggal cuti tidak boleh tanggal yang sudah lewat.',
        ]);

        $user = Auth::user();

        // 1. HITUNG DURASI BERSIH (EXCLUDE SABTU, MINGGU & LIBUR)
        $startDate = Carbon::parse($validatedData['tanggal_mulai']);
        $endDate   = Carbon::parse($validatedData['tanggal_selesai']);
        
        // [PERBAIKAN UTAMA] Ambil Libur Nasional di Range Tanggal & Format ke Y-m-d
        $holidays = Holiday::whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                        ->get()
                        ->map(function ($h) {
                            return Carbon::parse($h->tanggal)->format('Y-m-d');
                        })
                        ->toArray();
        
        $durasiEfektif = 0;
        $period = CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            // Cek: Apakah Weekend (Sabtu/Minggu) ATAU Tanggal Merah?
            if ($date->isWeekend() || in_array($date->format('Y-m-d'), $holidays)) {
                continue; // Skip
            }
            $durasiEfektif++;
        }

        // Jika user memilih tanggal yang isinya full libur
        if ($durasiEfektif === 0) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['tanggal_mulai' => 'Tanggal yang dipilih sepenuhnya adalah Hari Libur (Sabtu, Minggu, atau Tanggal Merah). Tidak perlu mengajukan cuti.']);
        }

        // 2. CEK SISA CUTI
        if (strtolower($validatedData['jenis_cuti']) == 'tahunan') {
            $tahunPengajuan = $startDate->year; 

            $cutiDisetujui = Cuti::where('user_id', $user->id)
                ->where('status', 'disetujui')
                ->whereYear('tanggal_mulai', $tahunPengajuan) 
                ->get();
            
            $terpakai = $this->hitungCutiTerpakai($cutiDisetujui);
            $terpakaiCount = $terpakai['tahunan'] ?? ($terpakai['Tahunan'] ?? 0);
            
            $jatahCuti = $user->jatah_cuti ?? 12;
            $sisaCuti  = $jatahCuti - $terpakaiCount;

            if ($durasiEfektif > $sisaCuti) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['tanggal_selesai' => "Durasi cuti efektif ($durasiEfektif hari kerja) melebihi sisa cuti Anda di tahun $tahunPengajuan (Sisa: $sisaCuti hari)."]);
            }
        }
        
        // 3. CEK TANGGAL BERTABRAKAN
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

        // 4. Proses Simpan
        $approver = $this->getApprover($user);
        if (!$approver) {
            return redirect()->back()->withInput()->with('error', 'Gagal: Atasan tidak ditemukan. Hubungi admin.');
        }

        $pathLampiran = $request->hasFile('lampiran') ? $request->file('lampiran')->store('lampiran_cuti', 'public') : null;

        $cuti = Cuti::create([
            'user_id'         => $user->id,
            'status'          => 'diajukan', 
            'jenis_cuti'      => $validatedData['jenis_cuti'],
            'tanggal_mulai'   => $validatedData['tanggal_mulai'],
            'tanggal_selesai' => $validatedData['tanggal_selesai'],
            'alasan'          => $validatedData['alasan'],
            'lampiran'        => $pathLampiran,
            'approver_1_id'   => $approver->id,
        ]);
        
        try {
            $approver->notify(new CutiNotification($cuti, 'baru'));
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
     */
    public function updateStatus(Request $request, Cuti $cuti)
    {
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
            
            // Format holiday ke Y-m-d untuk check
            $holidays = Holiday::whereBetween('tanggal', [$cuti->tanggal_mulai, $cuti->tanggal_selesai])
                ->get()
                ->map(fn($h) => Carbon::parse($h->tanggal)->format('Y-m-d'))
                ->toArray();

            foreach ($period as $date) {
                if ($date->isWeekend() || in_array($date->format('Y-m-d'), $holidays)) {
                    continue;
                }

                Absensi::updateOrCreate(
                    ['user_id' => $cuti->user_id, 'tanggal' => $date->format('Y-m-d')],
                    ['status' => 'cuti', 'keterangan' => 'Cuti ' . $cuti->jenis_cuti, 'jam_masuk' => '00:00:00']
                );
            }
        }
        
        try {
            Notification::send($cuti->user, new CutiNotification($cuti, $request->status));
        } catch (\Exception $e) {
            Log::error("Gagal kirim notif status: " . $e->getMessage());
        }

        return redirect()->route('cuti.show', $cuti)->with('success', 'Status pengajuan cuti berhasil diperbarui.');
    }

    public function cancel(Cuti $cuti)
    {
        if ($cuti->status === 'disetujui') {
            Absensi::where('user_id', $cuti->user_id)
                ->where('status', 'cuti')
                ->whereBetween('tanggal', [$cuti->tanggal_mulai, $cuti->tanggal_selesai])
                ->delete();
        }

        $cuti->update(['status' => 'dibatalkan']);

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

    public function download(Cuti $cuti)
    {
        if (Auth::id() !== $cuti->user_id && Auth::user()->role !== 'admin') {
             // abort(403);
        }
        
        $approver = $this->getApprover($cuti->user);
        $user = $cuti->user;
        
        $tahunCuti = Carbon::parse($cuti->tanggal_mulai)->year;

        $cutiDisetujui = Cuti::where('user_id', $user->id)
            ->where('status', 'disetujui')
            ->whereYear('tanggal_mulai', $tahunCuti)
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

    private function getApprover(User $user): ?User
    {
        if (!empty($user->approver_1_id)) return User::find($user->approver_1_id);
        if ($user->jabatan === 'Direktur') return null;
        if (str_starts_with($user->jabatan, 'Kepala')) return User::where('jabatan', 'Direktur')->first();
        
        if ($user->divisi) {
            $approver = User::where('divisi', $user->divisi)
                ->where('is_kepala_divisi', true)
                ->where('id', '!=', $user->id)
                ->first();
            if ($approver) return $approver;
        }

        return User::where('jabatan', 'Direktur')->first();
    }

    private function hitungCutiTerpakai(object $cutiCollection): array
    {
        $cutiTerpakai = [];
        
        // Ambil holidays tahun ini dan format ke Y-m-d
        $currentYearHolidays = Holiday::whereYear('tanggal', Carbon::now()->year)
            ->get()
            ->map(fn($h) => Carbon::parse($h->tanggal)->format('Y-m-d'))
            ->toArray();

        foreach ($cutiCollection as $cuti) {
            $start = Carbon::parse($cuti->tanggal_mulai);
            $end = Carbon::parse($cuti->tanggal_selesai);
            
            // Jika tahun beda, ambil lagi
            if ($start->year != Carbon::now()->year) {
                $holidays = Holiday::whereYear('tanggal', $start->year)
                    ->get()
                    ->map(fn($h) => Carbon::parse($h->tanggal)->format('Y-m-d'))
                    ->toArray();
            } else {
                $holidays = $currentYearHolidays;
            }

            $days = 0;
            $period = CarbonPeriod::create($start, $end);
            
            foreach ($period as $date) {
                // Skip Weekend & Holidays
                if ($date->isWeekend() || in_array($date->format('Y-m-d'), $holidays)) {
                    continue;
                }
                $days++;
            }
            
            $jenis = $cuti->jenis_cuti; 
            if (array_key_exists($jenis, $cutiTerpakai)) {
                $cutiTerpakai[$jenis] += $days;
            } else {
                $cutiTerpakai[$jenis] = $days;
            }
        }
        return $cutiTerpakai;
    }
}