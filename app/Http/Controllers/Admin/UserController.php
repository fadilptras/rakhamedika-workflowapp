<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Menampilkan daftar user berdasarkan role.
     */
    public function indexByRole($role)
    {
        if (!in_array($role, ['admin', 'user'])) {
            abort(404);
        }

        $users = User::where('role', $role)->latest()->paginate(10);
        $viewName = $role === 'admin' ? 'admin.admin' : 'admin.karyawan';
        $title = $role === 'admin' ? 'Kelola Admin' : 'Kelola Karyawan';

        return view($viewName, [
            'users' => $users,
            'title' => $title,
            'defaultRole' => $role
        ]);
    }

    /**
     * Simpan user baru.
     */
    public function store(Request $request)
    {
        // Logika IF untuk 'divisi_lainnya' dan request->merge() telah DIHAPUS
        // karena JavaScript di frontend sudah memastikan data yang dikirim benar.
        $validated = $request->validate([
            'name'                => ['required', 'string', 'max:255'],
            'email'               => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'            => ['required', 'string', 'min:8'],
            'role'                => ['required', Rule::in(['admin', 'user'])],
            'jabatan'             => 'nullable|string|max:255',
            'tanggal_bergabung'   => 'nullable|date',
            'profile_picture'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // Validasi disederhanakan. Controller hanya menerima satu field 'divisi'.
            'divisi'              => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $validated['password'] = Hash::make($validated['password']);

            if ($request->hasFile('profile_picture')) {
                $validated['profile_picture'] = $request->file('profile_picture')
                    ->store('profile-pictures', 'public');
            }

            User::create($validated);
        });

        $redirectRoute = $request->role === 'admin' ? 'admin.admins.index' : 'admin.employees.index';
        return redirect()->route($redirectRoute)->with('success', 'Akun berhasil ditambahkan.');
    }

    /**
     * Update user.
     */
    public function update(Request $request, User $user)
    {
        // Logika IF untuk 'divisi_lainnya' dan request->merge() juga DIHAPUS di sini.
        $validated = $request->validate([
            'name'                => ['required', 'string', 'max:255'],
            'email'               => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role'                => ['required', Rule::in(['admin', 'user'])],
            'password'            => ['nullable', 'string', 'min:8'],
            'jabatan'             => 'nullable|string|max:255',
            'tanggal_bergabung'   => 'nullable|date',
            'profile_picture'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // Validasi disederhanakan.
            'divisi'              => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $validated, $user) {
            if ($request->filled('password')) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            if ($request->hasFile('profile_picture')) {
                if ($user->profile_picture) {
                    Storage::disk('public')->delete($user->profile_picture);
                }
                $validated['profile_picture'] = $request->file('profile_picture')
                    ->store('profile-pictures', 'public');
            }

            $user->update($validated);
        });

        $redirectRoute = $request->role === 'admin' ? 'admin.admins.index' : 'admin.employees.index';
        return redirect()->route($redirectRoute)->with('success', 'Akun berhasil diupdate.');
    }

    /**
     * Hapus user.
     */
    public function destroy(User $user)
    {
        // Mencegah hapus admin utama (tidak ada perubahan di method ini)
        if ($user->email === 'admin@rakha.com') {
            return redirect()->back()->with('error', 'Aksi Ditolak! Akun admin utama tidak dapat dihapus.');
        }

        $role = $user->role;

        DB::transaction(function () use ($user) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $user->delete();
        });

        $redirectRoute = $role === 'admin' ? 'admin.admins.index' : 'admin.employees.index';
        return redirect()->route($redirectRoute)->with('success', 'Akun berhasil dihapus.');
    }
}