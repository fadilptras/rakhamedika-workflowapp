<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CutiNotification;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Storage;
use App\Models\Absensi;

class CutiController extends Controller
{
    /**
     * Menampilkan form pengajuan cuti dengan data sisa cuti dinamis.
     */
    public function create()
    {
        $totalCuti = [
            'tahunan' => 12,
        ];

        $user = Auth::user();
        $tahunIni = Carbon::now()->year;

        $cutiDisetujui = Cuti::where('user_id', $user->id)
                             ->where('status', 'disetujui')
                             ->whereYear('tanggal_mulai', $tahunIni)
                             ->get();

        $cutiTerpakai = $this->hitungCutiTerpakai($cutiDisetujui);

        $sisaCuti = [
            'tahunan' => $totalCuti['tahunan'] - $cutiTerpakai['tahunan'],
        ];

        $cutiRequests = Cuti::where('user_id', $user->id)->latest()->get();

        return view('users.cuti', [
            'title' => 'Pengajuan Cuti',
            'sisaCuti' => $sisaCuti,
            'totalCuti' => $totalCuti,
            'cutiRequests' => $cutiRequests,
        ]);
    }
    
    /**
     * Menampilkan halaman detail pengajuan cuti.
     */
    public function show(Cuti $cuti)
{
    $user = Auth::user();
    
    // Logika untuk menentukan approver
    $approver = $this->getApprover($cuti->user);

    // HRD dan approver bisa melihat detail, selain itu hanya pemilik cuti
    if ($user->id !== $cuti->user_id && $user->jabatan !== 'HRD' && (!$approver || $user->id !== $approver->id)) {
        abort(403, 'Anda tidak memiliki hak akses untuk melihat halaman ini.');
    }
    
    return view('users.detail-cuti', [
        'title' => 'Detail Pengajuan Cuti',
        'cuti' => $cuti,
        'approver' => $approver // Kirim data approver ke view
    ]);
    }

    /**
     * Menyimpan data pengajuan cuti baru ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'jenis_cuti' => 'required|in:tahunan',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string|max:2000',
            'lampiran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $user = Auth::user();
        $tahunIni = Carbon::now()->year;
        
        // Menghitung sisa cuti yang tersedia
        $cutiDisetujui = Cuti::where('user_id', $user->id)
                            ->where('status', 'disetujui')
                            ->whereYear('tanggal_mulai', $tahunIni)
                            ->get();
        $cutiTerpakai = $this->hitungCutiTerpakai($cutiDisetujui)['tahunan'];
        $sisaCuti = 12 - $cutiTerpakai;
        
        // Menghitung durasi cuti yang diajukan
        $durasiCuti = Carbon::parse($validatedData['tanggal_mulai'])->diffInDays(Carbon::parse($validatedData['tanggal_selesai'])) + 1;

        if ($durasiCuti > $sisaCuti) {
            return redirect()->back()->withInput()->withErrors(['tanggal_selesai' => 'Durasi cuti yang diajukan melebihi sisa cuti tahunan Anda (tersisa ' . $sisaCuti . ' hari).']);
        }
        
        $pathLampiran = null;
        if ($request->hasFile('lampiran')) {
            $pathLampiran = $request->file('lampiran')->store('lampiran_cuti', 'public');
        }

        $cuti = Cuti::create([
            'user_id' => Auth::id(),
            'status' => 'diajukan',
            'jenis_cuti' => $validatedData['jenis_cuti'],
            'tanggal_mulai' => $validatedData['tanggal_mulai'],
            'tanggal_selesai' => $validatedData['tanggal_selesai'],
            'alasan' => $validatedData['alasan'],
            'lampiran' => $pathLampiran,
        ]);

        // --- LOGIKA NOTIFIKASI BARU ---
        // 1. Cari approver (Kepala Divisi atau Direktur)
        $approver = $this->getApprover($user);

        // 2. Cari HRD
        $hrd = User::where('jabatan', 'HRD')->first();

        // 3. Kirim notifikasi
        if ($approver) {
            $approver->notify(new CutiNotification($cuti));
        }
        if ($hrd && (!$approver || $hrd->id !== $approver->id)) { // Hindari notif ganda jika approver adalah HRD
            $hrd->notify(new CutiNotification($cuti));
        }

        return redirect()->route('cuti')->with('success', 'Pengajuan cuti Anda telah berhasil dikirim dan menunggu persetujuan.');
    }

    /**
     * Mengubah status pengajuan cuti.
     */
    public function updateStatus(Request $request, Cuti $cuti)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string',
        ]);

        $currentUser = Auth::user();
        $pemohon = $cuti->user;

        // --- LOGIKA PERSETUJUAN BARU ---
        // 1. Tentukan siapa approver yang seharusnya
        $requiredApprover = $this->getApprover($pemohon);

        // 2. Validasi:
        // - Pastikan ada approver yang ditugaskan.
        // - Pastikan user yang login adalah approver yang benar.
        // - HRD tidak bisa approve.
        if (!$requiredApprover || $currentUser->id !== $requiredApprover->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah status pengajuan ini.');
        }

        if ($currentUser->jabatan === 'HRD') {
             abort(403, 'HRD tidak memiliki hak untuk menyetujui atau menolak pengajuan.');
        }

        // 3. Update status
        $cuti->update([
            'status' => $request->status,
            'catatan_approval' => $request->catatan,
        ]);

        // 4. Jika disetujui, otomatisasi absensi
        if ($request->status === 'disetujui') {
            $period = CarbonPeriod::create($cuti->tanggal_mulai, $cuti->tanggal_selesai);
            foreach ($period as $date) {
                Absensi::updateOrCreate(
                    [
                        'user_id' => $cuti->user_id,
                        'tanggal' => $date->format('Y-m-d'),
                    ],
                    [
                        'status' => 'cuti',
                        'keterangan' => 'Cuti ' . $cuti->jenis_cuti . ': ' . $cuti->alasan,
                        'jam_masuk' => '00:00:00',
                    ]
                );
            }
            return redirect()->route('cuti.show', $cuti)->with('success', 'Pengajuan cuti berhasil disetujui.');
        }

        return redirect()->route('cuti.show', $cuti)->with('success', 'Pengajuan cuti telah ditolak.');
    }

    /**
     * Helper function untuk mencari approver.
     * @param User $user User yang mengajukan cuti
     * @return User|null
     */
    private function getApprover(User $user): ?User
    {
        $approver = null;
        // Cari kepala divisi jika user punya divisi
        if ($user->divisi) {
            $approver = User::where('divisi', $user->divisi)
                            ->where('jabatan', 'like', 'Kepala%')
                            ->first();
        }

        // Jika tidak ada kepala divisi, cari Direktur
        if (!$approver) {
            $approver = User::where('jabatan', 'Direktur')->first();
        }

        return $approver;
    }

    /**
     * Helper function untuk menghitung total hari cuti yang terpakai.
     */
    private function hitungCutiTerpakai($cutiCollection): array
    {
        $cutiTerpakai = ['tahunan' => 0];

        foreach ($cutiCollection as $cuti) {
            $tanggalMulai = Carbon::parse($cuti->tanggal_mulai);
            $tanggalSelesai = Carbon::parse($cuti->tanggal_selesai);
            $durasi = $tanggalMulai->diffInDays($tanggalSelesai) + 1;

            if (array_key_exists($cuti->jenis_cuti, $cutiTerpakai)) {
                $cutiTerpakai[$cuti->jenis_cuti] += $durasi;
            }
        }

        return $cutiTerpakai;
    }
}