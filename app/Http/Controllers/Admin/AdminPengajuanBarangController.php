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

        // Ambil list karyawan untuk dropdown filter
        $karyawanList = User::where('role', 'user')->orderBy('name')->get();
        
        // Ambil list divisi (distinct) untuk dropdown filter
        $divisiList = User::select('divisi')->whereNotNull('divisi')->distinct()->get();

        $pengajuanBarangs = $query->paginate(10);

        return view('admin.pengajuan-barang.index', compact('pengajuanBarangs', 'activeTab', 'karyawanList', 'divisiList'));
    }

    /**
     * Setting approver barang per karyawan.
     */
    public function setApprovers()
    {
        $potentialApprovers = User::where('name', '!=', 'Admin Rakha')
                                  ->orderBy('name')
                                  ->get();

        return view('admin.pengajuan-barang.set-approvers', [
            'employees' => User::where('role', 'user')->orderBy('name')->get(),
            'approvers' => $potentialApprovers, 
            'title' => 'Set Approver Pengajuan Barang'
        ]);
    }

    /**
     * Simpan approver barang.
     */
    public function saveApprovers(Request $request)
    {
        $request->validate([
            'approver_barang_1.*' => 'nullable|exists:users,id',
            'approver_barang_2.*' => 'nullable|exists:users,id',
            'approver_barang_3.*' => 'nullable|exists:users,id',
        ]);

        DB::transaction(function () use ($request) {
            if ($request->has('approver_barang_1')) {
                foreach ($request->approver_barang_1 as $userId => $val) {
                    User::where('id', $userId)->update([
                        'approver_barang_1_id' => $request->approver_barang_1[$userId],
                        'approver_barang_2_id' => $request->approver_barang_2[$userId] ?? null,
                        'approver_barang_3_id' => $request->approver_barang_3[$userId] ?? null,
                    ]);
                }
            }
        });

        return redirect()->back()->with('success', 'Pengaturan Approver Barang berhasil diperbarui.');
    }

    public function show($id)
    {
        $pengajuanBarang = PengajuanBarang::with('user')->findOrFail($id);
        return view('admin.pengajuan-barang.show', compact('pengajuanBarang'));
    }

    public function downloadPdf($id) // Ubah ke $id untuk memastikan pencarian manual
    {
        // Cari data berdasarkan ID, jika tidak ada akan error 404 (lebih baik daripada PDF kosong)
        // Muat semua relasi: user (pemohon), dan para approver
        $pengajuan = PengajuanBarang::with(['user', 'approver1', 'approver2', 'approver3'])
                    ->findOrFail($id);
        
        // Pastikan variabel yang dikirim ke view adalah 'pengajuanBarang' 
        // sesuai dengan yang diminta oleh file pengajuan-barang.blade.php kamu
        $pdf = Pdf::loadView('pdf.pengajuan-barang', [
            'pengajuanBarang' => $pengajuan
        ])->setPaper('a4', 'portrait');

        $userName = str_replace(' ', '_', $pengajuan->user->name ?? 'User_Dihapus');
        $fileName = 'Pengajuan_Barang_' . $userName . '_' . str_pad($pengajuan->id, 4, '0', STR_PAD_LEFT) . '.pdf';

        return $pdf->download($fileName);
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