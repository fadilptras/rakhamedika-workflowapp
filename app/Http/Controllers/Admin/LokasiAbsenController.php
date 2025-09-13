<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LokasiAbsen;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class LokasiAbsenController extends Controller
{
    /**
     * Menampilkan daftar semua lokasi absen.
     */
    public function index()
    {
        $lokasi = LokasiAbsen::all();
        return view('admin.lokasi.index', compact('lokasi'));
    }

    /**
     * Menyimpan lokasi absen baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lokasi' => 'required|string',
            'latitude' => ['required', 'numeric', Rule::unique('lokasi_absen', 'latitude')->where('longitude', $request->longitude)],
            'longitude' => 'required|numeric',
            'radius_meter' => 'required|integer|min:1',
        ]);
    
        LokasiAbsen::create([
            'nama' => $request->nama_lokasi,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius_meter,
        ]);
    
        return redirect()->route('admin.lokasi.index')->with('success', 'Lokasi berhasil disimpan.');
    }
    
    /**
     * Memperbarui lokasi absen yang sudah ada.
     */
    public function update(Request $request, LokasiAbsen $lokasi)
    {
        $request->validate([
            'nama_lokasi' => 'required|string',
            'latitude' => ['required', 'numeric', Rule::unique('lokasi_absen', 'latitude')->ignore($lokasi->id)->where('longitude', $request->longitude)],
            'longitude' => 'required|numeric',
            'radius_meter' => 'required|integer|min:1',
        ]);
    
        $lokasi->update([
            'nama' => $request->nama_lokasi,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius_meter,
        ]);
    
        return redirect()->route('admin.lokasi.index')->with('success', 'Lokasi berhasil diperbarui.');
    }

    /**
     * Menghapus lokasi absen.
     */
    public function destroy(LokasiAbsen $lokasi)
    {
        try {
            $lokasi->delete();
            return redirect()->route('admin.lokasi.index')->with('success', 'Lokasi berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.lokasi.index')->with('error', 'Terjadi kesalahan saat menghapus lokasi.');
        }
    }
}