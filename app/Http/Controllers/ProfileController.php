<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman edit profil user yang sedang login.
     */
    public function editProfile()
    {
        $title = 'Edit Profil';
        $user = Auth::user();
        return view('users.profile', compact('title', 'user'));
    }

    /**
     * Memperbarui data profil user yang sedang login.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validasi data berdasarkan aturan di Admin\UserController.php
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'jabatan'           => 'nullable|string|max:255',
            'tanggal_bergabung' => 'nullable|date',
            'profile_picture'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::transaction(function () use ($request, $validated, $user) {
            // Logika update foto profil
            if ($request->hasFile('profile_picture')) {
                if ($user->profile_picture) {
                    Storage::disk('public')->delete($user->profile_picture);
                }
                $validated['profile_picture'] = $request->file('profile_picture')
                    ->store('profile-pictures', 'public');
            }

            // Logika untuk tanggal bergabung
            // Jika tanggal bergabung tidak diisi, hapus dari array agar tidak diupdate
            if (empty($validated['tanggal_bergabung'])) {
                unset($validated['tanggal_bergabung']);
            }
            
            // Kolom 'divisi' tidak diupdate oleh user
            // Hapus dari array yang akan diupdate
            if (isset($validated['divisi'])) {
                unset($validated['divisi']);
            }
            
            // Hapus password dari data yang diupdate, karena user tidak mengedit password di sini.
            if (isset($validated['password'])) {
                unset($validated['password']);
            }

            $user->update($validated);
        });

        return redirect()->route('editProfile')->with('success', 'Profil berhasil diupdate.');
    }
}