<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LokasiAbsen;
use Illuminate\Http\Request;

class LokasiAbsenController extends Controller
{
    /**
     * Menampilkan halaman pengaturan lokasi absensi.
     * Halaman ini akan menampilkan form untuk mengedit satu-satunya lokasi kantor.
     */
    public function index()
    {
        // Ambil data lokasi yang pertama, atau buat objek baru jika belum ada.
        // Ini memastikan view selalu menerima objek dan tidak akan error.
        $lokasi = LokasiAbsen::firstOrNew(['id' => 1]);
        
        return view('admin.lokasi.index', compact('lokasi'));
    }

    /**
     * Menyimpan atau memperbarui data lokasi absensi.
     * Hanya ada satu data yang akan dikelola.
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'nama_lokasi' => 'required|string|max:255',
            'latitude'    => 'required|numeric',
            'longitude'   => 'required|numeric',
            'radius_meter'=> 'required|integer|min:10', // Radius minimal 10 meter
        ], [
            'nama_lokasi.required' => 'Nama lokasi wajib diisi.',
            'latitude.required'    => 'Latitude tidak valid. Pastikan link Google Maps benar.',
            'longitude.required'   => 'Longitude tidak valid. Pastikan link Google Maps benar.',
            'radius_meter.required'=> 'Radius absen wajib diisi.',
            'radius_meter.min'     => 'Radius absen minimal adalah 10 meter.',
        ]);

        // Gunakan updateOrCreate untuk mengelola satu baris data secara efisien.
        // Ini akan mencari lokasi dengan ID 1, jika ada di-update, jika tidak maka dibuat.
        LokasiAbsen::updateOrCreate(
            ['id' => 1], // Kunci pencarian
            [
                'nama_lokasi' => $request->nama_lokasi,
                'latitude'    => $request->latitude,
                'longitude'   => $request->longitude,
                'radius_meter'=> $request->radius_meter,
            ]
        );

        // Kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', 'Pengaturan lokasi absensi berhasil diperbarui!');
    }
}