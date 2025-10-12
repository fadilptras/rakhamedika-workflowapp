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
    
        // Validasi utama tetap di sini
        $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'tanggal_bergabung' => 'nullable|date',
            'profile_picture'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            'nomor_telepon'         => 'nullable|string|max:20',
            'alamat'                => 'nullable|string',
            'tempat_lahir'          => 'nullable|string|max:255',
            'tanggal_lahir'         => 'nullable|date',
            'jenis_kelamin'         => 'nullable|string|in:Laki-laki,Perempuan',
            'nik'                   => 'nullable|string|max:20',
            'pendidikan_terakhir'   => 'nullable|string|max:255',
            'kontak_darurat_nama'   => 'nullable|string|max:255',
            'kontak_darurat_nomor'  => 'nullable|string|max:20',

            'current_password'  => ['nullable', 'required_with:password', 'string'],
            'password'          => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);
    
        // Jika pengguna mencoba mengubah password, validasi password lama
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password lama yang Anda masukkan tidak cocok.'])->withInput();
            }
        }
    
        DB::transaction(function () use ($request, $user) {
            $validated = $request->except('password', 'current_password', 'password_confirmation');

            if ($request->hasFile('profile_picture')) {
                if ($user->profile_picture) {
                    Storage::disk('public')->delete($user->profile_picture);
                }
                $validated['profile_picture'] = $request->file('profile_picture')
                    ->store('profile-pictures', 'public');
            }
    
            if ($request->filled('password')) {
                $validated['password'] = Hash::make($request->password);
            }
            
            unset($validated['jabatan'], $validated['divisi']);
    
            $user->update($validated);
        });
    
        return redirect()->route('profil.index')->with('success', 'Profil berhasil diupdate.');
    }

    // =======================================================
    // TAMBAHKAN FUNGSI BARU DI SINI
    // =======================================================
    /**
     * Memeriksa kecocokan password lama secara real-time (AJAX).
     */
    public function checkCurrentPassword(Request $request)
    {
        $request->validate(['current_password' => 'required|string']);

        $user = Auth::user();

        if (Hash::check($request->current_password, $user->password)) {
            return response()->json(['valid' => true]);
        }

        return response()->json(['valid' => false]);
    }
}