<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Menampilkan halaman dengan daftar karyawan (role = 'user').
     * Menggunakan view 'admin.dashboard'.
     */
    public function indexEmployees()
    {
        $users = User::where('role', 'user')->latest()->paginate(10);
        // UBAH DISINI: Arahkan ke view 'admin.dashboard'
        return view('admin.dashboard', [
            'users' => $users,
            'pageTitle' => 'Kelola Akun Karyawan',
            'defaultRole' => 'user'
        ]);
    }

    /**
     * Menampilkan halaman dengan daftar admin (role = 'admin').
     * Menggunakan view 'admin.dashboard'.
     */
    public function indexAdmins()
    {
        $users = User::where('role', 'admin')->latest()->paginate(10);
        // UBAH DISINI: Arahkan juga ke view 'admin.dashboard'
        return view('admin.dashboard', [
            'users' => $users,
            'pageTitle' => 'Kelola Akun Admin',
            'defaultRole' => 'admin'
        ]);
    }

    /**
     * Menyimpan user baru ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'user'])],
            'jabatan' => 'nullable|string|max:255',
            'tanggal_bergabung' => 'nullable|date',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validatedData['password'] = Hash::make($request->password);

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $validatedData['profile_picture'] = $path;
        }
        
        User::create($validatedData);

        // UBAH DISINI: Redirect berdasarkan role yang baru dibuat
        $redirectRoute = $request->role === 'admin' ? 'admin.admins.index' : 'admin.employees.index';
        return redirect()->route($redirectRoute)->with('success', 'Akun berhasil ditambahkan.');
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
            'password' => ['nullable', 'string', 'min:8'],
            'jabatan' => 'nullable|string|max:255',
            'tanggal_bergabung' => 'nullable|date',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($request->password);
        } else {
            unset($validatedData['password']);
        }

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $validatedData['profile_picture'] = $path;
        }
        
        $user->update($validatedData);

        // UBAH DISINI: Redirect berdasarkan role yang diupdate
        $redirectRoute = $request->role === 'admin' ? 'admin.admins.index' : 'admin.employees.index';
        return redirect()->route($redirectRoute)->with('success', 'Akun berhasil diupdate.');
    }

    /**
     * Menghapus user dari database.
     */
    public function destroy(User $user)
    {
        $role = $user->role; // Simpan role sebelum user dihapus

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $user->delete();

        // UBAH DISINI: Redirect berdasarkan role user yang dihapus
        $redirectRoute = $role === 'admin' ? 'admin.admins.index' : 'admin.employees.index';
        return redirect()->route($redirectRoute)->with('success', 'Akun berhasil dihapus.');
    }
}