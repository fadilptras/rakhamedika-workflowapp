<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Absensi;
use App\Models\User;
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

        // [BARU] Logika Tabulasi (Menggantikan Filter Status manual)
        // Default tab adalah 'pending' agar admin langsung melihat yang butuh tindakan
        $activeTab = $request->input('tab', 'pending'); 

        switch ($activeTab) {
            case 'pending':
                // Menampilkan cuti yang masih berstatus 'diajukan'
                $query->where('status', 'diajukan');
                break;
            case 'approved':
                // Menampilkan cuti yang sudah disetujui
                $query->whereIn('status', ['disetujui', 'diterima']);
                break;
            case 'rejected':
                // Menampilkan cuti yang ditolak
                $query->where('status', 'ditolak');
                break;
            default:
                // Jika tab 'all' atau tidak dikenali, tampilkan semua
                break;
        }

        // Filter Tambahan: Karyawan (User ID)
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter Tambahan: Tanggal
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal_mulai', [$request->tanggal_mulai, $request->tanggal_akhir]);
        }

        // Pagination
        $cutiRequests = $query->paginate(10)->withQueryString(); 
        
        // List user untuk dropdown filter
        $users = User::where('role', 'user')->orderBy('name')->get();

        return view('admin.cuti.index', [
            'title' => 'Manajemen Pengajuan Cuti',
            'cutiRequests' => $cutiRequests,
            'users' => $users, 
            'activeTab' => $activeTab // Mengirim data tab aktif ke view
        ]);
    }
    
    /**
     * Menampilkan detail pengajuan cuti untuk admin.
     */
    public function show(Cuti $cuti)
    {
        $title = 'Detail Pengajuan Cuti';
        return view('admin.cuti.show', compact('cuti', 'title'));
    }

    /**
     * Download PDF Formulir Cuti (Dilengkapi Sisa Cuti).
     */
    public function download(Cuti $cuti)
    {
        // 1. Ambil data approver (Atasan/Direktur)
        $approver = $this->getApprover($cuti->user);

        // 2. LOGIKA HITUNG SISA CUTI
        $user = $cuti->user;
        $tahunIni = \Carbon\Carbon::now()->year;

        // Hitung total hari cuti yang SUDAH DISETUJUI tahun ini milik user tersebut
        $cutiTerpakai = Cuti::where('user_id', $user->id)
            ->where('status', 'disetujui')
            ->whereYear('tanggal_mulai', $tahunIni)
            ->get()
            ->sum(function ($c) {
                $start = Carbon::parse($c->tanggal_mulai);
                $end = Carbon::parse($c->tanggal_selesai);
                return $start->diffInDays($end) + 1; // +1 agar inklusif
            });

        // Hitung Sisa
        $sisaCuti = ($user->jatah_cuti ?? 0) - $cutiTerpakai;

        // 3. Kirim ke View PDF
        $pdf = Pdf::loadView('pdf.cuti', [
            'cuti' => $cuti,
            'approver' => $approver,
            'sisaCuti' => $sisaCuti
        ]);
        
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download('ADMIN_Formulir-Cuti-' . $cuti->user->name . '-' . $cuti->created_at->format('dmY') . '.pdf');
    }

    /**
     * Helper untuk mendapatkan atasan (Logic approval).
     */
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

    /**
     * Mengubah status pengajuan cuti (Action Disetujui/Ditolak).
     */
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

        // Logika Manajer (Jika ada flow Manajer)
        if ($userJabatan === 'manajer' && $cuti->status_manajer === 'diajukan') {
            $cuti->update([
                'status_manajer' => $request->status,
                'catatan_manajer' => $catatan ?? ($request->status === 'disetujui' ? 'Disetujui oleh Manajer' : 'Ditolak oleh Manajer'),
            ]);

            if ($request->status === 'ditolak') {
                $cuti->update(['status' => 'ditolak']);
            }
            
            return redirect()->route('admin.cuti.show', $cuti)->with('success', 'Status pengajuan cuti berhasil diperbarui oleh Manajer.');
        }

        // Logika HRD (Final Approval)
        // HRD biasanya memproses setelah Manajer setuju, atau jika manajer skipped/tidak diperlukan
        // Asumsi logic: HRD bisa memproses jika status_manajer disetujui OR (jika flow manajer tidak wajib)
        if ($userJabatan === 'hrd' || $user->role === 'admin') { 
            // Cek apakah sudah disetujui manajer (jika diperlukan) atau langsung diproses
            
            $cuti->update([
                'status_hrd' => $request->status,
                'catatan_hrd' => $catatan ?? ($request->status === 'disetujui' ? 'Disetujui oleh HRD' : 'Ditolak oleh HRD'),
            ]);

            $cuti->update(['status' => $request->status]);

            // Jika disetujui HRD, masukkan ke tabel Absensi
            if ($request->status === 'disetujui') {
                 $period = CarbonPeriod::create($cuti->tanggal_mulai, $cuti->tanggal_selesai);
                 foreach ($period as $date) {
                     Absensi::updateOrCreate(
                         ['user_id' => $cuti->user_id, 'tanggal' => $date->format('Y-m-d')],
                         ['status' => 'cuti', 'keterangan' => 'Cuti ' . $cuti->jenis_cuti . ': ' . $cuti->alasan, 'jam_masuk' => '00:00:00']
                     );
                 }
            }
            
            return redirect()->route('admin.cuti.show', $cuti)->with('success', 'Status pengajuan cuti berhasil diperbarui oleh Admin/HRD.');
        }

        return redirect()->route('admin.cuti.show', $cuti)->with('error', 'Aksi tidak diizinkan atau pengajuan cuti belum pada tahap ini.');
    }

    /**
     * Menampilkan halaman pengaturan jatah cuti untuk admin.
     */
    public function pengaturanCuti()
    {
        // Ambil user dan hitung sisa cuti on-the-fly
        $users = User::where('role', 'user')->get()->map(function ($user) {
            
            // Hitung total hari cuti yang SUDAH DISETUJUI tahun ini
            $terpakai = Cuti::where('user_id', $user->id)
                ->where('status', 'disetujui')
                ->whereYear('tanggal_mulai', now()->year) // Hanya hitung tahun berjalan
                ->get()
                ->sum(function ($cuti) {
                    $start = Carbon::parse($cuti->tanggal_mulai);
                    $end = Carbon::parse($cuti->tanggal_selesai);
                    return $start->diffInDays($end) + 1; // +1 agar inklusif
                });

            // Attach data sementara ke object user
            $user->cuti_terpakai = $terpakai;
            $user->sisa_cuti = ($user->jatah_cuti ?? 0) - $terpakai;
            
            return $user;
        });

        return view('admin.cuti.pengaturan', [
            'title' => 'Pengaturan Jatah Cuti',
            'users' => $users
        ]);
    }

    /**
     * Menyimpan perubahan jatah cuti.
     */
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

    /**
     * [BARU] Download Rekap PDF (Laporan) sesuai Filter & Tab
     */
    public function downloadRekapPDF(Request $request)
    {
        $query = Cuti::with('user')->latest();

        // 1. Terapkan Filter TAB (Agar rekap sesuai tab yang dibuka)
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
            default:
                // All
                break;
        }

        // 2. Filter Karyawan & Tanggal
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal_mulai', [$request->tanggal_mulai, $request->tanggal_akhir]);
        }

        // Ambil Data (Get, bukan Paginate)
        $cutiRequests = $query->get();

        // Info untuk Header Laporan
        $userName = 'Semua Karyawan';
        if ($request->filled('user_id')) {
            $user = User::find($request->user_id);
            if ($user) $userName = $user->name;
        }
        
        $startDate = $request->tanggal_mulai;
        $endDate = $request->tanggal_akhir;

        // Load View PDF Rekap
        $pdf = Pdf::loadView('admin.cuti.pdf_rekap', compact(
            'cutiRequests', 
            'activeTab', 
            'userName', 
            'startDate', 
            'endDate'
        ));
        
        $pdf->setPaper('a4', 'landscape'); // Landscape agar muat banyak kolom
        
        $filename = "rekap-cuti-" . strtoupper($activeTab) . "-" . Carbon::now()->format('Y-m-d') . ".pdf";
        return $pdf->download($filename);
    }

    public function downloadPengaturanPDF()
    {
        // Ambil data user & hitung sisa cuti (Sama logic-nya dengan index pengaturan)
        $users = User::where('role', 'user')->orderBy('name')->get()->map(function ($user) {
            $tahunIni = now()->year;
            
            // Hitung cuti terpakai (status disetujui) di tahun ini
            $terpakai = Cuti::where('user_id', $user->id)
                ->where('status', 'disetujui')
                ->whereYear('tanggal_mulai', $tahunIni)
                ->get()
                ->sum(function ($cuti) {
                    $start = Carbon::parse($cuti->tanggal_mulai);
                    $end = Carbon::parse($cuti->tanggal_selesai);
                    return $start->diffInDays($end) + 1;
                });

            $user->cuti_terpakai = $terpakai;
            $user->sisa_cuti = ($user->jatah_cuti ?? 0) - $terpakai;
            
            return $user;
        });

        $pdf = Pdf::loadView('admin.cuti.pdf_pengaturan', [
            'users' => $users,
            'tahun' => now()->year
        ]);
        
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download('Laporan-Sisa-Cuti-Karyawan-' . now()->format('Y') . '.pdf');
    }
}