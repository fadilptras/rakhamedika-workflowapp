<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanDana;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminPengajuanDanaController extends Controller
{
    /**
     * Menampilkan daftar semua pengajuan dana (untuk admin).
     */
    public function index(Request $request)
    {
        $query = PengajuanDana::with('user')->latest();

        if ($request->filled('karyawan_id')) {
            $query->where('user_id', $request->karyawan_id);
        }
        if ($request->filled('divisi')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('divisi', $request->divisi);
            });
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        $pengajuanDanas = $query->paginate(10)->appends($request->query());

        $karyawanList = User::where('role', 'user')->orderBy('name')->get();
        $divisiList = User::where('role', 'user')
                            ->whereNotNull('divisi')
                            ->select('divisi')
                            ->distinct()
                            ->orderBy('divisi')
                            ->get();

        return view('admin.pengajuan-dana.index', [
            'title' => 'Kelola Pengajuan Dana',
            'pengajuanDanas' => $pengajuanDanas,
            'karyawanList' => $karyawanList, 
            'divisiList' => $divisiList,   
        ]);
    }

    /**
     * Menampilkan detail pengajuan dana (untuk admin).
     */
    public function show(PengajuanDana $pengajuanDana)
    {
        $pengajuanDana->load(['user', 'approver1', 'approver2', 'financeProcessor', 'user.managerKeuangan']);
        
        return view('admin.pengajuan-dana.show', [
            'title' => 'Detail Pengajuan Dana',
            'pengajuanDana' => $pengajuanDana,
        ]);
    }

    /**
     * Mengunduh PDF detail pengajuan dana (untuk admin).
     */
    public function downloadPDF(PengajuanDana $pengajuanDana)
    {
        // [FIX] Gunakan nama relasi yang benar dari Model PengajuanDana
        $pengajuanDana->load(['user', 'approver1', 'approver2', 'financeProcessor', 'user.managerKeuangan']);
        
        $pdf = PDF::loadView('pdf.pdf_pengajuan_dana', compact('pengajuanDana'));
        $namaJudul = \Illuminate\Support\Str::slug($pengajuanDana->judul_pengajuan, '-');
        $filename = "pengajuan-dana-{$pengajuanDana->id}-{$namaJudul}.pdf";
        return $pdf->download($filename);
    }

    /**
     * Mengunduh rekap pengajuan dana dalam bentuk PDF (untuk admin).
     */
    public function downloadRekapPDF(Request $request)
    {
        $query = PengajuanDana::with('user')->latest();
        $startDate = null; $endDate = null;
        $karyawanId = $request->input('karyawan_id'); // [FIX] Ambil ID karyawan dari request
        $divisi = $request->input('divisi');         // [FIX] Ambil nama divisi dari request

        // --- Filter Query ---
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        if ($karyawanId) { // [FIX] Filter berdasarkan ID jika ada
            $query->where('user_id', $karyawanId);
        }
        if ($divisi) { // [FIX] Filter berdasarkan divisi jika ada
            $query->whereHas('user', function($q) use ($divisi) {
                $q->where('divisi', $divisi);
            });
        }
        
        $pengajuanDanas = $query->get();

        // --- [FIX] Ambil Nama untuk Tampilan PDF ---
        $karyawanName = 'Semua Karyawan'; // Default
        if ($karyawanId) {
            $karyawan = User::find($karyawanId);
            if ($karyawan) {
                $karyawanName = $karyawan->name;
            }
        }
        $divisiName = $divisi ?: 'Semua Divisi'; // Gunakan nilai divisi dari request, atau default

        // --- Load View PDF ---
        // [FIX] Tambahkan $karyawanName dan $divisiName ke compact()
        $pdf = PDF::loadView('admin.pengajuan-dana.pdf_rekap', compact(
            'pengajuanDanas', 
            'startDate', 
            'endDate', 
            'karyawanName', 
            'divisiName'
        ));
        
        $filename = "rekap-pengajuan-dana-" . Carbon::now()->format('Y-m-d') . ".pdf";
        return $pdf->download($filename);
    }

    /**
     * Menampilkan halaman pengaturan approver (untuk admin).
     */
    public function showSetApprovers()
    {
        $employees = User::where('role', 'user')->orderBy('name')->get();

        $approvers = User::where('role', 'admin')
                         ->orWhere('is_kepala_divisi', true)
                         ->orWhereIn('jabatan', ['Direktur']) 
                         ->orderBy('name')
                         ->get();

        $financeManagers = $approvers; // Samakan list finance manager dengan approver

        return view('admin.pengajuan-dana.set-approvers', [
            'title' => 'Atur Alur Persetujuan Karyawan',
            'employees' => $employees,
            'approvers' => $approvers,
            'financeManagers' => $financeManagers,
        ]);
    }

    /**
     * Menyimpan pengaturan approver (untuk admin).
     */
    public function saveSetApprovers(Request $request)
    {
        $request->validate([
            'approver_1' => 'required|array',
            'approver_2' => 'required|array',
            'manager_keuangan' => 'required|array', 
            'approver_1.*' => 'nullable|exists:users,id',
            'approver_2.*' => 'nullable|exists:users,id|different:approver_1.*', 
            'manager_keuangan.*' => 'nullable|exists:users,id', 
        ], [
            'approver_2.*.different' => 'Approver 1 dan Approver 2 tidak boleh orang yang sama.',
        ]);

        $approver1Data = $request->input('approver_1');
        $approver2Data = $request->input('approver_2');
        $managerKeuanganData = $request->input('manager_keuangan'); 

        DB::beginTransaction();
        try {
            foreach ($approver1Data as $userId => $approver1Id) {
                $user = User::find($userId);
                if ($user) {
                    $user->approver_1_id = $approver1Id; // Akan null jika "-- Tidak Ada --"

                    // Pastikan key ada sebelum mengakses
                    $user->approver_2_id = $approver2Data[$userId] ?? null; 
                    $user->manager_keuangan_id = $managerKeuanganData[$userId] ?? null;
                    
                    $user->save();
                }
            }
            DB::commit();
            return redirect()->route('admin.pengajuan_dana.set_approvers.index')->with('success', 'Pengaturan alur persetujuan berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            // Sebaiknya log error ini
            // Log::error('Gagal simpan approver: ' . $e->getMessage());
            return redirect()->route('admin.pengajuan_dana.set_approvers.index')->with('error', 'Terjadi kesalahan. Perubahan dibatalkan.');
        }
    }
}