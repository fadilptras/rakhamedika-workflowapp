<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminPengajuanDokumenController extends Controller
{
    public function index()
    {
        $pengajuanDokumens = PengajuanDokumen::with('user')->latest()->get();
        return view('admin.pengajuan-dokumen.index', compact('pengajuanDokumens'));
    }

    public function show(PengajuanDokumen $dokumen)
    {
        return view('admin.pengajuan-dokumen.show', ['pengajuanDana' => $dokumen]);
    }

    public function update(Request $request, PengajuanDokumen $dokumen)
    {
        $validated = $request->validate([
            'status' => 'required|in:diproses,selesai,ditolak',
            'catatan_admin' => 'nullable|string',
            'file_hasil' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($request->hasFile('file_hasil')) {
            // Hapus file lama jika ada
            if ($dokumen->file_hasil) {
                Storage::disk('public')->delete($dokumen->file_hasil);
            }
            // Simpan file baru
            $validated['file_hasil'] = $request->file('file_hasil')->store('dokumen_hasil', 'public');
        }
        
        $dokumen->update($validated);

        return redirect()->route('admin.pengajuan-dokumen.show', $dokumen)->with('success', 'Status pengajuan berhasil diperbarui!');
    }
}