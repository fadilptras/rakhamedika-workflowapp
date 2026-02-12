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

class ProfileController extends Controller
{
    public function editProfile()
    {
        $title = 'Edit Profil';
        $user = Auth::user()->load('riwayatPendidikan', 'riwayatPekerjaan');
        return view('users.profile', compact('title', 'user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password'          => ['nullable', 'string', 'min:8', 'confirmed'],
            'profile_picture'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'file_ktp'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_npwp'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_bpjs_kesehatan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_bpjs_ketenagakerjaan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'pendidikan.*.file_ijazah' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        DB::transaction(function () use ($request, $user) {
            // 1. Update Data Dasar (Kecuali File & Relasi)
            $userData = $request->except([
                'profile_picture', 'file_ktp', 'file_npwp', 
                'file_bpjs_kesehatan', 'file_bpjs_ketenagakerjaan', 
                'pendidikan', 'pekerjaan', 'password', 'password_confirmation'
            ]);

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            // 2. Handle Foto Profil
            if ($request->hasFile('profile_picture')) {
                if ($user->profile_picture) {
                    Storage::disk('public')->delete($user->profile_picture);
                }
                $userData['profile_picture'] = $request->file('profile_picture')->store('profile_pictures', 'public');
            }

            // 3. Handle Dokumen Pribadi (Looping agar efisien)
            $dokumenFields = ['file_ktp', 'file_npwp', 'file_bpjs_kesehatan', 'file_bpjs_ketenagakerjaan'];
            foreach ($dokumenFields as $field) {
                if ($request->hasFile($field)) {
                    if ($user->$field) {
                        Storage::disk('public')->delete($user->$field);
                    }
                    $userData[$field] = $request->file($field)->store('dokumen_karyawan', 'public');
                }
            }

            $user->update($userData);

            // 4. Handle Riwayat Pendidikan (Termasuk Ijazah)
            $submittedPendidikanIds = [];
            if ($request->has('pendidikan')) {
                foreach ($request->pendidikan as $index => $pnd) {
                    $dataPnd = [
                        'jenjang'         => $pnd['jenjang'],
                        'nama_institusi'  => $pnd['nama_institusi'],
                        'jurusan'         => $pnd['jurusan'],
                        'tahun_lulus'     => $pnd['tahun_lulus'],
                    ];

                    // Cek upload ijazah menggunakan dot notation untuk array
                    if ($request->hasFile("pendidikan.$index.file_ijazah")) {
                        $file = $request->file("pendidikan.$index.file_ijazah");
                        
                        // Hapus file lama jika sedang mengedit (ada ID)
                        if (isset($pnd['id'])) {
                            $oldPnd = RiwayatPendidikan::find($pnd['id']);
                            if ($oldPnd && $oldPnd->file_ijazah) {
                                Storage::disk('public')->delete($oldPnd->file_ijazah);
                            }
                        }
                        $dataPnd['file_ijazah'] = $file->store('dokumen_karyawan/ijazah', 'public');
                    }

                    if (isset($pnd['id'])) {
                        $pendidikan = RiwayatPendidikan::findOrFail($pnd['id']);
                        $pendidikan->update($dataPnd);
                        $submittedPendidikanIds[] = $pendidikan->id;
                    } else {
                        $newPnd = $user->riwayatPendidikan()->create($dataPnd);
                        $submittedPendidikanIds[] = $newPnd->id;
                    }
                }
            }
            $user->riwayatPendidikan()->whereNotIn('id', $submittedPendidikanIds)->delete();

            // 5. Handle Riwayat Pekerjaan
            $submittedPekerjaanIds = [];
            if ($request->has('pekerjaan')) {
                foreach ($request->pekerjaan as $pkj) {
                    if (isset($pkj['id'])) {
                        $pekerjaan = RiwayatPekerjaan::findOrFail($pkj['id']);
                        $pekerjaan->update($pkj);
                        $submittedPekerjaanIds[] = $pekerjaan->id;
                    } else {
                        $newPkj = $user->riwayatPekerjaan()->create($pkj);
                        $submittedPekerjaanIds[] = $newPkj->id;
                    }
                }
            }
            $user->riwayatPekerjaan()->whereNotIn('id', $submittedPekerjaanIds)->delete();
        });

        return redirect()->route('profil.index')->with('success', 'Profil dan dokumen berhasil diperbarui.');
    }
}               