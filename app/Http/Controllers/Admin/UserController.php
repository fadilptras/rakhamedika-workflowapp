<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * menampilkan user berdasarkan role
     */
    public function indexByRole($role)
    {
        if (!in_array($role, ['admin', 'user'])) {
            abort(404);
        }

        // --- PERUBAHAN DIMULAI DI SINI ---
        // 1. Ambil semua user tanpa paginasi, urutkan berdasarkan divisi lalu nama.
        $users = User::where('role', $role)->orderBy('divisi')->orderBy('name')->get();

        // 2. Kelompokkan user berdasarkan 'divisi'.
        //    Gunakan fungsi callback untuk menangani divisi yang kosong/null.
        $usersByDivision = $users->groupBy(function ($user) {
            return $user->divisi ?: 'Tanpa Divisi'; // Jika divisi null atau kosong, kelompokkan ke 'Tanpa Divisi'
        });

        $viewName = $role === 'admin' ? 'admin.admin' : 'admin.karyawan';
        $title = $role === 'admin' ? 'Kelola Admin' : 'Kelola Karyawan';

        return view($viewName, [
            'usersByDivision' => $usersByDivision, // 3. Kirim data yang sudah dikelompokkan ke view
            'title' => $title,
            'defaultRole' => $role
        ]);
        // --- AKHIR PERUBAHAN ---
    }


    /**
     * save user baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'          => ['required', 'string', 'min:8'],
            'role'              => ['required', Rule::in(['admin', 'user'])],
            'jabatan'           => 'nullable|string|max:255',
            'tanggal_bergabung' => 'nullable|date',
            'profile_picture'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'divisi'            => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $validated['password'] = Hash::make($validated['password']);

            if ($request->hasFile('profile_picture')) {
                $validated['profile_picture'] = $request->file('profile_picture')
                    ->store('profile-pictures', 'public');
            }

            // Jika tanggal bergabung tidak diisi saat membuat user baru, default ke hari ini
            if (empty($validated['tanggal_bergabung'])) {
                $validated['tanggal_bergabung'] = Carbon::now();
            }

            User::create($validated);
        });

        $redirectRoute = $request->role === 'admin' ? 'admin.admins.index' : 'admin.employees.index';
        return redirect()->route($redirectRoute)->with('success', 'Akun berhasil ditambahkan.');
    }

    /**
     * update user.
     */
    public function update(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role'              => ['required', Rule::in(['admin', 'user'])],
            'password'          => ['nullable', 'string', 'min:8'],
            'jabatan'           => 'nullable|string|max:255',
            'tanggal_bergabung' => 'nullable|date',
            'profile_picture'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'divisi'            => 'nullable|string|max:255',
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
            
            if (empty($validated['tanggal_bergabung'])) {
                unset($validated['tanggal_bergabung']);
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

        /**
     * Mengatur seorang user sebagai Kepala Divisi.
     * Ini akan secara otomatis menghapus status kepala divisi dari user lain di divisi yang sama.
     */
    public function setAsDivisionHead(User $user)
    {
        // Pastikan user punya divisi
        if (empty($user->divisi)) {
            return redirect()->back()->with('error', 'Tidak bisa mengatur kepala divisi untuk karyawan tanpa divisi.');
        }

        DB::transaction(function () use ($user) {
            // 1. Reset semua kepala divisi di divisi yang sama
            User::where('divisi', $user->divisi)
                ->where('id', '!=', $user->id)
                ->update(['is_kepala_divisi' => false]);

            // 2. Atur user yang dipilih sebagai kepala divisi
            $user->update(['is_kepala_divisi' => true]);
        });

        return redirect()->route('admin.employees.index')->with('success', "{$user->name} telah diatur sebagai Kepala Divisi {$user->divisi}.");
    }
}