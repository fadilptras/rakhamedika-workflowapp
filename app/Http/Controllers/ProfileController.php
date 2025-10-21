<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\RiwayatPendidikan;
use App\Models\RiwayatPekerjaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;


class ProfileController extends Controller
{
    /**
     * Menampilkan halaman edit profil user yang sedang login.
     */
    public function editProfile()
    {
        $title = 'Edit Profil';
        // --- UBAH QUERY INI ---
        // Muat user beserta relasi riwayatnya
        $user = Auth::user()->load('riwayatPendidikan', 'riwayatPekerjaan');
        // ---------------------
        return view('users.profile', compact('title', 'user'));
    }

    /**
     * Memperbarui data profil user yang sedang login.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
    
        // --- VALIDASI DIPERBARUI TOTAL ---
        $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'profile_picture'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            // Info Pribadi
            'nik'               => 'nullable|string|max:20',
            'nomor_telepon'     => 'nullable|string|max:20',
            'tempat_lahir'      => 'nullable|string|max:255',
            'tanggal_lahir'     => 'nullable|date',
            'jenis_kelamin'     => 'nullable|string|in:Laki-laki,Perempuan',
            'agama'             => 'nullable|string|max:50',
            'status_pernikahan' => 'nullable|string|max:50',
            'golongan_darah'    => 'nullable|string|max:10',
            'alamat_ktp'        => 'nullable|string',
            'alamat_domisili'   => 'nullable|string',
            
            // Info Administrasi & Bank
            'npwp'                  => 'nullable|string|max:100',
            'ptkp'                  => 'nullable|string|max:20',
            'bpjs_kesehatan'        => 'nullable|string|max:100',
            'bpjs_ketenagakerjaan'  => 'nullable|string|max:100',
            'nama_bank'             => 'nullable|string|max:100',
            'nomor_rekening'        => 'nullable|string|max:100',
            'pemilik_rekening'      => 'nullable|string|max:255',

            // Kontak Darurat
            'kontak_darurat_nama'   => 'nullable|string|max:255',
            'kontak_darurat_nomor'  => 'nullable|string|max:20',
            'kontak_darurat_hubungan' => 'nullable|string|max:100',

            // Validasi Password
            'current_password'  => ['nullable', 'required_with:password', 'string'],
            'password'          => ['nullable', 'string', 'min:8', 'confirmed'],

            // Validasi Array Riwayat
            'pendidikan'                => 'nullable|array',
            'pendidikan.*.jenjang'      => 'required_with:pendidikan|string|max:100',
            'pendidikan.*.nama_institusi' => 'required_with:pendidikan|string|max:255',
            'pendidikan.*.jurusan'      => 'nullable|string|max:255',
            'pendidikan.*.tahun_lulus'  => 'nullable|string|max:4',
            
            'pekerjaan'                     => 'nullable|array',
            'pekerjaan.*.nama_perusahaan'   => 'required_with:pekerjaan|string|max:255',
            'pekerjaan.*.posisi'            => 'required_with:pekerjaan|string|max:255',
            'pekerjaan.*.tanggal_mulai'     => 'nullable|date',
            'pekerjaan.*.tanggal_selesai'   => 'nullable|date|after_or_equal:pekerjaan.*.tanggal_mulai',
            'pekerjaan.*.deskripsi_pekerjaan' => 'nullable|string',
        ]);
        // --- AKHIR VALIDASI BARU ---
    
        // Validasi password lama (Logika ini sudah benar)
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password lama yang Anda masukkan tidak cocok.'])->withInput();
            }
        }
    
        DB::transaction(function () use ($request, $user) {
            // 1. Ambil data HANYA untuk tabel 'users'
            $userData = $request->except([
                'password', 'current_password', 'password_confirmation', 
                'profile_picture', 'pendidikan', 'pekerjaan' // Kecualikan data array
            ]);

            // 2. Handle Upload Foto (Logika ini sudah benar)
            if ($request->hasFile('profile_picture')) {
                if ($user->profile_picture) {
                    Storage::disk('public')->delete($user->profile_picture);
                }
                $userData['profile_picture'] = $request->file('profile_picture')
                    ->store('profile-pictures', 'public');
            }
    
            // 3. Handle Password Baru (Logika ini sudah benar)
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            
            // 4. Hapus field yang tidak boleh diubah user
            unset(
                $userData['jabatan'], $userData['divisi'], $userData['nip'], 
                $userData['status_karyawan'], $userData['atasan_id'], 
                $userData['lokasi_kerja'], $userData['tanggal_bergabung'], 
                $userData['tanggal_mulai_kontrak'], $userData['tanggal_akhir_kontrak'],
                $userData['tanggal_berhenti']
            );
    
            // 5. Update data utama user
            $user->update($userData);

            // 6. PROSES RIWAYAT PENDIDIKAN (BARU)
            $submittedPendidikanIds = [];
            if ($request->has('pendidikan')) {
                foreach ($request->pendidikan as $key => $data) {
                    // Cek jika 'id' ada (untuk update) atau tidak (untuk create)
                    $id = $data['id'] ?? null;
                    
                    // updateOrCreate akan meng-update jika ID cocok, atau membuat baru jika ID null
                    $pendidikan = $user->riwayatPendidikan()->updateOrCreate(
                        ['id' => $id], // Kunci untuk mencari
                        $data         // Data untuk diisi/diupdate
                    );
                    $submittedPendidikanIds[] = $pendidikan->id;
                }
            }
            // Hapus riwayat pendidikan yang tidak ada di form (dihapus oleh user)
            $user->riwayatPendidikan()->whereNotIn('id', $submittedPendidikanIds)->delete();


            // 7. PROSES RIWAYAT PEKERJAAN (BARU)
            $submittedPekerjaanIds = [];
            if ($request->has('pekerjaan')) {
                foreach ($request->pekerjaan as $key => $data) {
                    $id = $data['id'] ?? null;
                    $pekerjaan = $user->riwayatPekerjaan()->updateOrCreate(
                        ['id' => $id],
                        $data
                    );
                    $submittedPekerjaanIds[] = $pekerjaan->id;
                }
            }
            // Hapus riwayat pekerjaan yang tidak ada di form
            $user->riwayatPekerjaan()->whereNotIn('id', $submittedPekerjaanIds)->delete();

        }); // Akhir DB::transaction
    
        return redirect()->route('profil.index')->with('success', 'Profil berhasil diupdate.');
    }
    public function checkCurrentPassword(Request $request)
    {
        $request->validate(['current_password' => 'required|string']);

        $user = Auth::user();

        if (Hash::check($request->current_password, $user->password)) {
            return response()->json(['valid' => true]);
        }

        return response()->json(['valid' => false]);
    }

    public function downloadPdf()
    {
        // 1. Ambil data user yang sedang login beserta relasinya
        $user = Auth::user()->load('riwayatPendidikan', 'riwayatPekerjaan');

        // 2. Siapkan data untuk view PDF
        $data = [
            'user' => $user,
            'tanggal_cetak' => now()->translatedFormat('d F Y H:i') // Format tanggal Indonesia
        ];

        // 3. Load view PDF dengan data
        //    Pastikan Anda membuat folder 'pdf' di dalam 'resources/views/'
        $pdf = PDF::loadView('pdf.profile', $data);

        // 4. Buat nama file dinamis
        $namaFile = 'Profil Karyawan - ' . $user->name . ' - ' . now()->format('Ymd') . '.pdf';

        // 5. Kembalikan sebagai download
        return $pdf->download($namaFile);
    }
}