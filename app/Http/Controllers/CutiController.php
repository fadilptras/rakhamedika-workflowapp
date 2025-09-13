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
        // Pastikan hanya pemilik atau approver yang bisa melihat
        $user = Auth::user();
        if ($user->id !== $cuti->user_id && !in_array($user->jabatan, ['Manajer', 'HRD'])) {
            abort(403);
        }
        
        return view('users.detail-cuti', [
            'title' => 'Detail Pengajuan Cuti',
            'cuti' => $cuti,
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
            'status_manajer' => 'diajukan',
            'status_hrd' => 'diajukan',
            'jenis_cuti' => $validatedData['jenis_cuti'],
            'tanggal_mulai' => $validatedData['tanggal_mulai'],
            'tanggal_selesai' => $validatedData['tanggal_selesai'],
            'alasan' => $validatedData['alasan'],
            'lampiran' => $pathLampiran,
        ]);

        // Ambil manajer dan HRD untuk dikirim notifikasi berdasarkan jabatan
        $manajer = User::where('jabatan', 'Manajer')->first();
        $hrd = User::where('jabatan', 'HRD')->first();

        // Kirim notifikasi ke manajer dan HRD
        if ($manajer) {
            $manajer->notify(new CutiNotification($cuti));
        }
        if ($hrd) {
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

        $user = Auth::user();
        $isApproved = $request->status === 'disetujui';
        
        // Menambahkan validasi agar yang boleh approve hanya Manajer dan HRD
        if (!in_array($user->jabatan, ['Manajer', 'HRD'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk menyetujui pengajuan cuti.');
        }

        // Jika HRD menolak, langsung tolak pengajuan
        if ($user->jabatan === 'HRD' && $request->status === 'ditolak') {
            $cuti->update([
                'status_hrd' => 'ditolak',
                'catatan_hrd' => $request->catatan ?? 'Ditolak',
                'status' => 'ditolak',
            ]);
            return redirect()->route('cuti.show', $cuti)->with('success', 'Pengajuan cuti telah ditolak oleh HRD.');
        }

        // Perbarui status sesuai jabatan
        if ($user->jabatan === 'Manajer' && $cuti->status_manajer === 'diajukan') {
            $cuti->update([
                'status_manajer' => $request->status,
                'catatan_manajer' => $request->catatan ?? ($isApproved ? 'Disetujui' : 'Ditolak'),
            ]);
        } elseif ($user->jabatan === 'HRD' && $cuti->status_hrd === 'diajukan') {
            $cuti->update([
                'status_hrd' => $request->status,
                'catatan_hrd' => $request->catatan ?? ($isApproved ? 'Disetujui' : 'Ditolak'),
            ]);
        } else {
            return redirect()->route('cuti.show', $cuti)->with('error', 'Aksi tidak diizinkan atau pengajuan cuti sudah diproses.');
        }

        $cuti->refresh(); // Ambil data terbaru setelah di-update

        // Jika salah satu menolak, status akhir langsung ditolak.
        if ($cuti->status_manajer === 'ditolak' || $cuti->status_hrd === 'ditolak') {
            $cuti->update(['status' => 'ditolak']);
            return redirect()->route('cuti.show', $cuti)->with('success', 'Pengajuan cuti telah ditolak.');
        }

        // Cek apakah keduanya sudah menyetujui
        if ($cuti->status_manajer === 'disetujui' && $cuti->status_hrd === 'disetujui') {
            $cuti->update(['status' => 'disetujui']);

            // Otomatisasi absensi jika disetujui
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
            return redirect()->route('cuti.show', $cuti)->with('success', 'Pengajuan cuti berhasil disetujui oleh Manajer dan HRD.');
        }

        return redirect()->route('cuti.show', $cuti)->with('success', 'Status pengajuan cuti berhasil diperbarui.');
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