<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman form login.
     */
    public function index()
    {
        return view('auth.login'); // Kita akan buat view ini di Langkah 2
    }

    /**
     * Memproses percobaan login.
     */
    public function authenticate(Request $request)
    {
        // 1. Validasi input
        $credentials = $request->validate([
            'email' => ['required', 'email'],               
            'password' => ['required'],
        ]);

        // 2. Coba lakukan autentikasi
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // 3. Cek role dan redirect sesuai rolenya
            $user = Auth::user();

            if ($user->role === 'admin') {
                return redirect()->intended('/admin/dashboard'); // Arahkan ke dashboard admin
            }

            // Jika bukan admin, arahkan ke dashboard user
            return redirect()->intended('/dashboard'); 
        }

        // 4. Jika gagal, kembali ke halaman login dengan pesan error
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Memproses logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login'); // Redirect ke halaman login setelah logout
    }
}