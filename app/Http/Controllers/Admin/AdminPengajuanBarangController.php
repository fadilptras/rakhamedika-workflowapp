<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanBarang;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminPengajuanBarangController extends Controller
{
    /**
     * Menampilkan daftar pengajuan barang dengan tabulasi.
     */
    public function index(Request $request)
    {
        $query = PengajuanBarang::with('user')->latest();
        $activeTab = $request->input('tab', 'pending'); 

        switch ($activeTab) {
            case 'pending':
                $query->whereIn('status', ['diajukan', 'diproses']);
                break;
            case 'approved':
                $query->where('status', 'selesai');
                break;
            case 'rejected':
                $query->whereIn('status', ['ditolak', 'dibatalkan']);
                break;
        }

        if ($request->filled('karyawan_id')) {
            $query->where('user_id', $request->karyawan_id);
        }

        // --- TAMBAHKAN BAGIAN INI ---
        // Ambil list karyawan untuk dropdown filter
        $karyawanList = User::where('role', 'user')->orderBy('name')->get();
        
        // Ambil list divisi (distinct) untuk dropdown filter
        // Pastikan kolom 'divisi' ada di tabel users
        $divisiList = User::select('divisi')->whereNotNull('divisi')->distinct()->get();
        // -----------------------------

        $pengajuanBarangs = $query->paginate(10);

        // Tambahkan variabel baru ke compact()
        return view('admin.pengajuan-barang.index', compact('pengajuanBarangs', 'activeTab', 'karyawanList', 'divisiList'));
    }

    /**
     * HALAMAN BARU: Setting approver barang per karyawan.
     */
    public function setApprovers()
    {
        // Ambil semua user kecuali yang bernama 'Admin Rakha'
        // Jika ingin mengecualikan user yang sedang login juga, tambahkan: ->where('id', '!=', Auth::id())
        $potentialApprovers = User::where('name', '!=', 'Admin Rakha')
                                  ->orderBy('name')
                                  ->get();

        return view('admin.pengajuan-barang.set-approvers', [
            'employees' => User::where('role', 'user')->orderBy('name')->get(),
            'approvers' => $potentialApprovers, // Variable ini sekarang berisi seluruh karyawan
            'title' => 'Set Approver Pengajuan Barang'
        ]);
    }

    /**
     * METHODO BARU: Simpan approver barang.
     */
    public function saveApprovers(Request $request)
    {
        $request->validate([
            'approver_barang_1.*' => 'nullable|exists:users,id',
            'approver_barang_2.*' => 'nullable|exists:users,id',
        ]);

        DB::transaction(function () use ($request) {
            if ($request->has('approver_barang_1')) {
                foreach ($request->approver_barang_1 as $userId => $val) {
                    User::where('id', $userId)->update([
                        'approver_barang_1_id' => $request->approver_barang_1[$userId],
                        'approver_barang_2_id' => $request->approver_barang_2[$userId] ?? null,
                    ]);
                }
            }
        });

        return redirect()->back()->with('success', 'Pengaturan Approver Barang berhasil diperbarui.');
    }

    /**
     * UPDATE: Logika Approve Barang Dinamis.
     */
    public function approve(Request $request, $id)
    {
        $pengajuan = PengajuanBarang::findOrFail($id);
        $user = $pengajuan->user;
        $adminId = Auth::id();

        if ($adminId == $user->approver_barang_1_id) {
            $pengajuan->update([
                'status_approver_1' => 'disetujui',
                'tanggal_approve_1' => now()
            ]);
        } elseif ($adminId == $user->approver_barang_2_id) {
            $pengajuan->update([
                'status_approver_2' => 'disetujui',
                'tanggal_approve_2' => now()
            ]);
        } else {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses sebagai approver untuk karyawan ini.');
        }

        // Jika keduanya setuju, pengajuan selesai
        if ($pengajuan->status_approver_1 === 'disetujui' && $pengajuan->status_approver_2 === 'disetujui') {
            $pengajuan->update(['status' => 'selesai']);
        }

        return redirect()->back()->with('success', 'Persetujuan berhasil diproses.');
    }

    public function show($id)
    {
        $pengajuanBarang = PengajuanBarang::with('user')->findOrFail($id);
        return view('admin.pengajuan-barang.show', compact('pengajuanBarang'));
    }

    public function reject(Request $request, $id)
    {
        $pengajuan = PengajuanBarang::findOrFail($id);
        $pengajuan->update([
            'status' => 'ditolak',
            'alasan_penolakan' => $request->alasan_penolakan
        ]);

        return redirect()->back()->with('success', 'Pengajuan barang telah ditolak.');
    }

    public function downloadPdf($id)
    {
        $pengajuan = PengajuanBarang::with('user')->findOrFail($id);
        $pdf = Pdf::loadView('pdf.pengajuan-barang', compact('pengajuan'));
        return $pdf->download('Pengajuan_Barang_' . $pengajuan->user->name . '.pdf');
    }

    public function downloadRekapPdf(Request $request)
    {
        $query = PengajuanBarang::with('user');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }

        if ($request->filled('karyawan_id')) {
            $query->where('user_id', $request->karyawan_id);
        }

        $pengajuanBarangs = $query->get();
        $pdf = Pdf::loadView('admin.pengajuan-barang.pdf_rekap', compact('pengajuanBarangs'));
        
        return $pdf->download('Rekap_Pengajuan_Barang.pdf');
    }

    /**
     * Menghapus pengajuan barang beserta lampirannya.
     */
    public function destroy($id)
    {
        $pengajuan = PengajuanBarang::findOrFail($id);

        // Hapus file lampiran jika ada
        if ($pengajuan->lampiran) {
            foreach ($pengajuan->lampiran as $file) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($file)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($file);
                }
            }
        }

        // Hapus Data dari Database
        $pengajuan->delete();

        return redirect()->back()->with('success', 'Data pengajuan barang berhasil dihapus.');
    }
}