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
// --- TAMBAHKAN USE STATEMENT INI ---
use Barryvdh\DomPDF\Facade\Pdf;
// ------------------------------------
// (Hapus use Auth jika tidak digunakan di method lain di controller ini)
use Illuminate\Support\Facades\Auth; 

class UserController extends Controller
{
    /**
     * Menampilkan user berdasarkan role (Admin atau Karyawan).
     */
    public function indexByRole($role)
    {
        if (!in_array($role, ['admin', 'user'])) {
            abort(404);
        }

        if ($role === 'admin') {
            $users = User::where('role', 'admin')->orderBy('name')->get();
            return view('admin.admin', [
                'users' => $users,
                'title' => 'Kelola Admin',
                'defaultRole' => 'admin'
            ]);
        }

        if ($role === 'user') {
            // --- EAGER LOADING DITAMBAHKAN DI SINI ---
            $users = User::where('role', 'user')
                         ->orderBy('divisi')
                         ->orderBy('name')
                         ->with('riwayatPendidikan', 'riwayatPekerjaan') // <-- Eager Load
                         ->get();
            // ----------------------------------------

            $usersByDivision = $users->groupBy(function ($user) {
                return $user->divisi ?: 'Tanpa Divisi';
            });

            return view('admin.karyawan', [
                'usersByDivision' => $usersByDivision,
                'title' => 'Kelola Karyawan',
                'defaultRole' => 'user'
            ]);
        }
    }

    /**
     * Menyimpan user baru.
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
            // 'profile_picture'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Dihapus karena foto tidak di-upload di form ini
            'divisi'            => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $validated['password'] = Hash::make($validated['password']);

            // Hapus logika upload foto jika tidak ada field-nya
            // if ($request->hasFile('profile_picture')) { ... }

            if (empty($validated['tanggal_bergabung'])) {
                $validated['tanggal_bergabung'] = Carbon::now();
            }

            User::create($validated);
        });

        $redirectRoute = $request->role === 'admin' ? 'admin.admins.index' : 'admin.employees.index';
        return redirect()->route($redirectRoute)->with('success', 'Akun berhasil ditambahkan.');
    }

    /**
     * Mengupdate data user.
     */
    public function update(Request $request)
    {
        // Pastikan user_id ada dalam request
        $request->validate(['user_id' => 'required|exists:users,id']);
        $user = User::findOrFail($request->user_id);

        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role'              => ['required', Rule::in(['admin', 'user'])],
            'password'          => ['nullable', 'string', 'min:8'],
            'jabatan'           => 'nullable|string|max:255',
            'tanggal_bergabung' => 'nullable|date',
            // 'profile_picture'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Dihapus jika tidak ada
            'divisi'            => 'nullable|string|max:255',
            'user_id'           => 'required|exists:users,id' // Validasi user_id
        ]);

        DB::transaction(function () use ($request, $validated, $user) {
            if ($request->filled('password')) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']); // Jangan update password jika kosong
            }

            // Hapus logika update foto jika tidak ada
            // if ($request->hasFile('profile_picture')) { ... }

            // Jangan hapus tanggal bergabung jika kosong, biarkan nilai lama
            if (empty($validated['tanggal_bergabung'])) {
                 unset($validated['tanggal_bergabung']);
            }

            // Hapus user_id dari data yang akan diupdate
            unset($validated['user_id']);

            $user->update($validated);
        });

        $redirectRoute = $request->role === 'admin' ? 'admin.admins.index' : 'admin.employees.index';
        return redirect()->route($redirectRoute)->with('success', 'Akun berhasil diupdate.');
    }


    /**
     * Menghapus user.
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
     * Mengatur user sebagai Kepala Divisi.
     */
    public function setAsDivisionHead(User $user)
    {
        if (empty($user->divisi)) {
            return redirect()->back()->with('error', 'Tidak bisa mengatur kepala divisi untuk karyawan tanpa divisi.');
        }

        DB::transaction(function () use ($user) {
            User::where('divisi', $user->divisi)
                ->where('id', '!=', $user->id)
                ->update(['is_kepala_divisi' => false]);
            $user->update(['is_kepala_divisi' => true]);
        });

        return redirect()->route('admin.employees.index')->with('success', "{$user->name} telah diatur sebagai Kepala Divisi {$user->divisi}.");
    }

    // --- === METHOD BARU UNTUK DOWNLOAD PDF === ---
    /**
     * Menghasilkan dan mengunduh PDF profil karyawan spesifik.
     */
    public function downloadProfilePdf(User $user)
    {
        // 1. Muat relasi yang dibutuhkan (sudah dimuat di indexByRole, tapi kita muat lagi untuk keamanan)
        $user->load('riwayatPendidikan', 'riwayatPekerjaan');

        // 2. Siapkan data untuk view
        $data = [
            'user' => $user,
            // Ambil nama user yang mencetak (admin yang sedang login)
            'pencetak' => Auth::user()->name,
            'tanggal_cetak' => now()->translatedFormat('d F Y H:i')
        ];

        // 3. Load view PDF ('pdf.profile' yang sama dengan ProfileController)
        $pdf = Pdf::loadView('pdf.profile', $data)->setPaper('a4', 'portrait');

        // 4. Buat nama file
        $namaFile = 'Profil Karyawan - ' . $user->name . ' - ' . now()->format('Ymd') . '.pdf';

        // 5. Kembalikan sebagai download
        return $pdf->download($namaFile);
    }
    // --- === AKHIR METHOD BARU === ---
}