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
    
        // Validasi data profil, termasuk password yang opsional
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'tanggal_bergabung' => 'nullable|date',
            'profile_picture'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password'          => ['nullable', 'string', 'min:8'],
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
    
            // Jika password diisi, enkripsi dan update
            if ($request->filled('password')) {
                $validated['password'] = Hash::make($request->password);
            } else {
                // Jika password kosong, hapus dari array agar tidak diupdate
                unset($validated['password']);
            }
    
            // Jika tanggal bergabung kosong, hapus dari array agar tidak di-update
            if (empty($validated['tanggal_bergabung'])) {
                unset($validated['tanggal_bergabung']);
            }
            
            // Kolom jabatan dan divisi tidak boleh diubah oleh user, hapus jika ada di request
            if (isset($validated['jabatan'])) {
                unset($validated['jabatan']);
            }
            if (isset($validated['divisi'])) {
                unset($validated['divisi']);
            }
    
            $user->update($validated);
        });
    
        return redirect()->route('profil.index')->with('success', 'Profil berhasil diupdate.');
    }
}