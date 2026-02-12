<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Absensi;
use App\Models\User;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; 
use Barryvdh\DomPDF\Facade\Pdf;

class CutiController extends Controller
{
    /**
     * Menampilkan daftar semua pengajuan cuti dengan sistem Tab.
     */
    public function index(Request $request)
    {
        $query = Cuti::with('user')->latest();
        $activeTab = $request->input('tab', 'pending'); 

        switch ($activeTab) {
            case 'pending':
                $query->where('status', 'diajukan');
                break;
            case 'approved':
                $query->where('status', 'disetujui');
                break;
            case 'rejected':
                $query->where('status', 'ditolak');
                break;
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal_mulai', [$request->tanggal_mulai, $request->tanggal_akhir]);
        }

        $cutiRequests = $query->paginate(10)->withQueryString(); 
        $users = User::where('role', 'user')->orderBy('name')->get();

        return view('admin.cuti.index', [
            'title' => 'Manajemen Pengajuan Cuti',
            'cutiRequests' => $cutiRequests,
            'users' => $users, 
            'activeTab' => $activeTab
        ]);
    }
    
    /**
     * HALAMAN BARU: Menampilkan list untuk setting approver cuti.
     */
    public function setApprovers()
    {
        // Ambil semua user kecuali 'Admin Rakha' agar bisa dipilih sebagai approver
        $potentialApprovers = User::where('name', '!=', 'Admin Rakha')
                                  ->orderBy('name')
                                  ->get();

        return view('admin.cuti.set-approvers', [
            'employees' => User::where('role', 'user')->orderBy('name')->get(),
            'approvers' => $potentialApprovers, // Variabel ini sekarang berisi seluruh karyawan (kecuali admin rakha)
            'title' => 'Set Approver Pengajuan Cuti'
        ]);
    }

    /**
     * METHODO BARU: Menyimpan perubahan approver cuti per karyawan.
     */
    public function saveApprovers(Request $request)
    {
        $request->validate([
            'approver_cuti_1.*' => 'nullable|exists:users,id',
            'approver_cuti_2.*' => 'nullable|exists:users,id',
        ]);

        DB::transaction(function () use ($request) {
            if ($request->has('approver_cuti_1')) {
                foreach ($request->approver_cuti_1 as $userId => $val) {
                    User::where('id', $userId)->update([
                        'approver_cuti_1_id' => $request->approver_cuti_1[$userId],
                        'approver_cuti_2_id' => $request->approver_cuti_2[$userId] ?? null,
                    ]);
                }
            }
        });

        return redirect()->back()->with('success', 'Pengaturan Approver Cuti berhasil diperbarui.');
    }
    
    public function show(Cuti $cuti)
    {
        $title = 'Detail Pengajuan Cuti';
        return view('admin.cuti.show', compact('cuti', 'title'));
    }

    /**
     * Download PDF Formulir Cuti
     */
    public function download(Cuti $cuti)
    {
        // [HAPUS/GANTI BARIS INI]
        // $approver = $this->getApprover($cuti->user); 

        // [GANTI DENGAN INI]
        // Ambil Approver 1 dari data user pemohon cuti
        $approver = User::find($cuti->user->approver_cuti_1_id);

        $user = $cuti->user;
        
        // Hitung sisa cuti berdasarkan TAHUN PENGAJUAN CUTI tersebut
        $tahunCuti = Carbon::parse($cuti->tanggal_mulai)->year;

        // Ambil cuti yang disetujui di tahun yang sama dengan cuti ini
        $cutiSetahun = Cuti::where('user_id', $user->id)
            ->where('status', 'disetujui')
            ->whereYear('tanggal_mulai', $tahunCuti)
            ->get();

        // Hitung hari efektif (Skip Libur/Minggu)
        $terpakai = $this->hitungHariEfektif($cutiSetahun);

        $sisaCuti = ($user->jatah_cuti ?? 12) - $terpakai;

        $pdf = Pdf::loadView('pdf.cuti', [
            'cuti' => $cuti,
            'approver' => $approver, // Kirim object user approver ke view
            'sisaCuti' => $sisaCuti
        ]);
        
        $pdf->setPaper('a4', 'portrait');
        return $pdf->download('ADMIN_Formulir-Cuti-' . $cuti->user->name . '.pdf');
    }


    /**
     * [PENTING] Halaman Pengaturan Jatah Cuti
     * Di sini logika hitung sisa cuti diperbaiki agar konsisten.
     */
    public function pengaturanCuti()
    {
        // 1. Tentukan Tahun Ini (Server Time)
        $tahunIni = Carbon::now()->year;

        // 2. Ambil user dan hitung sisa cuti on-the-fly dengan logika BARU
        $users = User::where('role', 'user')->get()->map(function ($user) use ($tahunIni) {
            
            // Ambil semua cuti disetujui di tahun ini
            $cutiSetahun = Cuti::where('user_id', $user->id)
                ->where('status', 'disetujui')
                ->whereYear('tanggal_mulai', $tahunIni) // Filter Reset Tahunan
                ->get();

            // Hitung pakai fungsi helper (Exclude Libur)
            $terpakai = $this->hitungHariEfektif($cutiSetahun);

            $user->cuti_terpakai = $terpakai;
            $user->sisa_cuti = ($user->jatah_cuti ?? 12) - $terpakai;
            
            return $user;
        });

        return view('admin.cuti.pengaturan', [
            'title' => 'Pengaturan Jatah Cuti (' . $tahunIni . ')',
            'users' => $users
        ]);
    }

    public function updatePengaturanCuti(Request $request)
    {
        $request->validate([
            'jatah_cuti' => 'required|array',
            'jatah_cuti.*' => 'required|integer|min:0',
        ]);

        foreach ($request->jatah_cuti as $userId => $jatahCuti) {
            $user = User::find($userId);
            if ($user) {
                $user->jatah_cuti = $jatahCuti;
                $user->save();
            }
        }
        return redirect()->route('admin.cuti.pengaturan')->with('success', 'Jatah cuti berhasil diperbarui.');
    }

    public function downloadRekapPDF(Request $request)
    {
        $query = Cuti::with('user')->latest();
        $activeTab = $request->input('tab', 'pending'); 

        switch ($activeTab) {
            case 'pending': $query->where('status', 'diajukan'); break;
            case 'approved': $query->whereIn('status', ['disetujui', 'diterima']); break;
            case 'rejected': $query->where('status', 'ditolak'); break;
        }

        if ($request->filled('user_id')) $query->where('user_id', $request->user_id);
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal_mulai', [$request->tanggal_mulai, $request->tanggal_akhir]);
        }

        $cutiRequests = $query->get();
        $userName = 'Semua Karyawan';
        if ($request->filled('user_id')) {
            $user = User::find($request->user_id);
            if ($user) $userName = $user->name;
        }
        
        $startDate = $request->tanggal_mulai;
        $endDate = $request->tanggal_akhir;

        $pdf = Pdf::loadView('admin.cuti.pdf_rekap', compact('cutiRequests', 'activeTab', 'userName', 'startDate', 'endDate'));
        $pdf->setPaper('a4', 'landscape'); 
        return $pdf->download("rekap-cuti-" . strtoupper($activeTab) . "-" . Carbon::now()->format('Y-m-d') . ".pdf");
    }

    /**
     * Download Laporan Sisa Cuti
     */
    public function downloadPengaturanPDF()
    {
        $tahunIni = Carbon::now()->year;

        $users = User::where('role', 'user')->orderBy('name')->get()->map(function ($user) use ($tahunIni) {
            
            $cutiSetahun = Cuti::where('user_id', $user->id)
                ->where('status', 'disetujui')
                ->whereYear('tanggal_mulai', $tahunIni)
                ->get();

            // Hitung pakai fungsi helper (Exclude Libur)
            $terpakai = $this->hitungHariEfektif($cutiSetahun);

            $user->cuti_terpakai = $terpakai;
            $user->sisa_cuti = ($user->jatah_cuti ?? 12) - $terpakai;
            
            return $user;
        });

        $pdf = Pdf::loadView('admin.cuti.pdf_pengaturan', [
            'users' => $users,
            'tahun' => $tahunIni
        ]);
        
        $pdf->setPaper('a4', 'portrait');
        return $pdf->download('Laporan-Sisa-Cuti-Karyawan-' . $tahunIni . '.pdf');
    }

    /**
     * HELPER BARU: Menghitung Durasi Bersih (Tanpa Minggu/Libur)
     * Digunakan berulang kali agar logic konsisten
     */
    private function hitungHariEfektif($cutiCollection)
    {
        $totalDays = 0;
        
        // Ambil semua libur tahun ini sekali saja untuk efisiensi
        $holidays = Holiday::whereYear('tanggal', Carbon::now()->year)->pluck('tanggal')->toArray();

        foreach ($cutiCollection as $cuti) {
            $start = Carbon::parse($cuti->tanggal_mulai);
            $end = Carbon::parse($cuti->tanggal_selesai);
            
            $period = CarbonPeriod::create($start, $end);
            
            foreach ($period as $date) {
                // Skip Minggu (0) & Libur Nasional
                if ($date->isSunday() || in_array($date->format('Y-m-d'), $holidays)) {
                    continue;
                }
                $totalDays++;
            }
        }
        return $totalDays;
    }

    /**
     * Menghapus pengajuan cuti.
     */
    public function destroy($id)
    {
        $cuti = Cuti::findOrFail($id);

        // Opsi: Hapus file lampiran jika ada
        if ($cuti->lampiran && \Illuminate\Support\Facades\Storage::disk('public')->exists($cuti->lampiran)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($cuti->lampiran);
        }

        // Opsi: Hapus data absensi terkait jika statusnya sudah disetujui
        if ($cuti->status == 'disetujui') {
            Absensi::where('user_id', $cuti->user_id)
                ->where('status', 'cuti')
                ->whereBetween('tanggal', [$cuti->tanggal_mulai, $cuti->tanggal_selesai])
                ->delete();
        }

        $cuti->delete();

        return redirect()->back()->with('success', 'Data pengajuan cuti berhasil dihapus.');
    }
}