<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanDokumen;
use Illuminate\Http\Request; // Import Request
use Illuminate\Support\Facades\Storage;

class AdminPengajuanDokumenController extends Controller
{
    public function index(Request $request) // Tambahkan Request $request
    {
        $query = PengajuanDokumen::with('user');

        // --- TAMBAHAN BARU: LOGIKA FILTER TANGGAL ---
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->start_date . ' 00:00:00';
            $endDate = $request->end_date . ' 23:59:59';
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $pengajuanDokumens = $query->latest()->get();
        
        return view('admin.pengajuan-dokumen.index', compact('pengajuanDokumens'));
    }

    public function show(PengajuanDokumen $dokumen)
    {
        // Variabel diubah agar konsisten dengan file view lain
        return view('admin.pengajuan-dokumen.show', ['dokumen' => $dokumen]);
    }

    public function update(Request $request, PengajuanDokumen $dokumen)
    {
        $validated = $request->validate([
            'status' => 'required|in:diproses,selesai,ditolak',
            'catatan_admin' => 'nullable|string',
            'file_hasil' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($request->hasFile('file_hasil')) {
            if ($dokumen->file_hasil) {
                Storage::disk('public')->delete($dokumen->file_hasil);
            }
            $validated['file_hasil'] = $request->file('file_hasil')->store('dokumen_hasil', 'public');
        }
        
        $dokumen->update($validated);

        return redirect()->route('admin.pengajuan-dokumen.show', $dokumen)->with('success', 'Status pengajuan berhasil diperbarui!');
    }
}