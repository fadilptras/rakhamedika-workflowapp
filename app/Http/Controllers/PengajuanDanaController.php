<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanDana;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PengajuanDanaController extends Controller
{
    /**
     * Menampilkan halaman formulir pengajuan dana dengan riwayat.
     */
    public function index()
    {
        $title = 'Pengajuan Dana';
        $pengajuanDanas = Auth::user()->pengajuanDanas()->orderBy('created_at', 'desc')->get();
        return view('users.pengajuan-dana', compact('title', 'pengajuanDanas'));
    }

    /**
     * Menampilkan halaman detail pengajuan dana.
     */
    public function show(PengajuanDana $pengajuanDana)
    {
        if (Auth::id() !== $pengajuanDana->user_id) {
            abort(403);
        }

        return view('users.detail-pengajuan-dana', [
            'title' => 'Detail Pengajuan Dana',
            'pengajuanDana' => $pengajuanDana,
        ]);
    }
    
    /**
     * Menyimpan data pengajuan dana yang baru.
     */
    public function store(Request $request)
    {
        // Logika validasi dan penyimpanan data yang sudah kita buat sebelumnya
        $validatedData = $request->validate([
            'judul_pengajuan' => 'required|string|max:255',
            'divisi' => 'required|string|max:255',
            'nama_bank' => 'required_if:nama_bank_lainnya,null|nullable|string|max:255',
            'nama_bank_lainnya' => 'required_if:nama_bank,other|nullable|string|max:255',
            'no_rekening' => 'required|string|max:255',
            'jumlah_dana_total' => 'required|numeric|min:1',
            'rincian_deskripsi.*' => 'required|string|max:1000',
            'rincian_jumlah.*' => 'required|numeric|min:1',
            'file_pendukung' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);
    
        $rincian = [];
        foreach ($request->input('rincian_deskripsi') as $key => $deskripsi) {
            $rincian[] = [
                'deskripsi' => $deskripsi,
                'jumlah' => $request->input('rincian_jumlah')[$key],
            ];
        }
    
        $pathFile = null;
        if ($request->hasFile('file_pendukung')) {
            $pathFile = $request->file('file_pendukung')->store('lampiran_dana', 'public');
        }
    
        PengajuanDana::create([
            'user_id' => Auth::id(),
            'judul_pengajuan' => $validatedData['judul_pengajuan'],
            'divisi' => $validatedData['divisi'],
            'nama_bank' => $validatedData['nama_bank'] === 'other' ? $validatedData['nama_bank_lainnya'] : $validatedData['nama_bank'],
            'no_rekening' => $validatedData['no_rekening'],
            'total_dana' => $validatedData['jumlah_dana_total'],
            'rincian_dana' => $rincian,
            'lampiran' => $pathFile,
        ]);
    
        return redirect()->route('pengajuan_dana.index')->with('success', 'Pengajuan dana berhasil dikirim!');
    }
}