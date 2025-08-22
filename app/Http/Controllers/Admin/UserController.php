<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage; // <-- UBAH DISINI: Tambahkan ini untuk mengelola file
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Menampilkan halaman daftar user di Dashboard.
     */
    public function index()
    {
        $users = User::latest()->paginate(10); 
        return view('admin.dashboard', compact('users'));
    }

    /**
     * Menyimpan user baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi input 
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'user'])],
            'jabatan' => 'nullable|string|max:255',
            'tanggal_bergabung' => 'nullable|date',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // max 2MB
        ]);

        // hash password
        $validatedData['password'] = Hash::make($request->password);

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $validatedData['profile_picture'] = $path;
        }
        
        User::create($validatedData);

        return redirect()->route('admin.dashboard')->with('success', 'Akun berhasil ditambahkan.');
    }

    /**
     * Mengupdate data user di database.
     */
    public function update(Request $request, User $user)
    {

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'user'])],
            'password' => ['nullable', 'string', 'min:8'], // Password jadi opsional
            'jabatan' => 'nullable|string|max:255',
            'tanggal_bergabung' => 'nullable|date',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

    
        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($request->password);
        } else {
            unset($validatedData['password']);
        }

        // 3. Cek jika ada file gambar baru yang di-upload
        if ($request->hasFile('profile_picture')) {
            // Hapus gambar lama jika ada
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            // Simpan gambar baru dan dapatkan path-nya
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $validatedData['profile_picture'] = $path;
        }
        
        // 4. Update data user
        $user->update($validatedData);

        // 5. Redirect dengan pesan sukses
        return redirect()->route('admin.dashboard')->with('success', 'Akun berhasil diupdate.');
    }

    /**
     * Menghapus user dari database.
     */
    public function destroy(User $user)
    {
        // <-- UBAH DISINI: Hapus juga foto profilnya dari storage -->
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        // Hapus data user dari database
        $user->delete();

        // Redirect dengan pesan sukses
        return redirect()->route('admin.dashboard')->with('success', 'Akun berhasil dihapus.');
    }
}