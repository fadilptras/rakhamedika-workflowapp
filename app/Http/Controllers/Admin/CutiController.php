<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Absensi;
use App\Models\User;
use App\Models\Holiday; // [PENTING] Tambahkan Model Holiday
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
                $query->whereIn('status', ['disetujui', 'diterima']);
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
        $approver = $this->getApprover($cuti->user);
        $user = $cuti->user;
        
        // [UPDATE] Hitung sisa cuti berdasarkan TAHUN PENGAJUAN CUTI tersebut
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
            'approver' => $approver,
            'sisaCuti' => $sisaCuti
        ]);
        
        $pdf->setPaper('a4', 'portrait');
        return $pdf->download('ADMIN_Formulir-Cuti-' . $cuti->user->name . '.pdf');
    }

    private function getApprover(User $user): ?User
    {
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

    public function updateStatus(Request $request, Cuti $cuti)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan_persetujuan' => 'nullable|string|max:1000',
            'catatan_penolakan' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $userJabatan = strtolower($user->jabatan);
        $catatan = $request->status === 'disetujui' ? $request->catatan_persetujuan : $request->catatan_penolakan;

        // Logic Approval (Manajer/HRD) disederhanakan sesuai kode Anda sebelumnya
        if ($userJabatan === 'manajer' && $cuti->status_manajer === 'diajukan') {
            $cuti->update([
                'status_manajer' => $request->status,
                'catatan_manajer' => $catatan ?? ($request->status === 'disetujui' ? 'Disetujui oleh Manajer' : 'Ditolak oleh Manajer'),
            ]);
            if ($request->status === 'ditolak') $cuti->update(['status' => 'ditolak']);
            return redirect()->route('admin.cuti.show', $cuti)->with('success', 'Status diperbarui Manajer.');
        }

        if ($userJabatan === 'hrd' || $user->role === 'admin') { 
            $cuti->update([
                'status_hrd' => $request->status,
                'catatan_hrd' => $catatan ?? ($request->status === 'disetujui' ? 'Disetujui oleh HRD' : 'Ditolak oleh HRD'),
                'status' => $request->status
            ]);

            // Jika disetujui, Masukkan ke Absensi (SKIP HARI LIBUR & MINGGU)
            if ($request->status === 'disetujui') {
                 $period = CarbonPeriod::create($cuti->tanggal_mulai, $cuti->tanggal_selesai);
                 $holidays = Holiday::whereBetween('tanggal', [$cuti->tanggal_mulai, $cuti->tanggal_selesai])
                            ->pluck('tanggal')->toArray();

                 foreach ($period as $date) {
                     // [FIX] Jangan buat absen cuti di hari libur/minggu
                     if ($date->isSunday() || in_array($date->format('Y-m-d'), $holidays)) {
                        continue;
                     }

                     Absensi::updateOrCreate(
                         ['user_id' => $cuti->user_id, 'tanggal' => $date->format('Y-m-d')],
                         ['status' => 'cuti', 'keterangan' => 'Cuti ' . $cuti->jenis_cuti . ': ' . $cuti->alasan, 'jam_masuk' => '00:00:00']
                     );
                 }
            }
            return redirect()->route('admin.cuti.show', $cuti)->with('success', 'Status diperbarui Admin/HRD.');
        }

        return redirect()->route('admin.cuti.show', $cuti)->with('error', 'Aksi tidak diizinkan.');
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
     * [PENTING] Download Laporan Sisa Cuti
     * Update logika agar sama persis dengan tampilan web
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
}