<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AbsenController; // Pastikan ini ada
use App\Http\Controllers\PengajuanDanaController;
use App\Http\Controllers\PengajuanDokumenController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\CutiController;

// Route utama, langsung arahkan ke halaman login
Route::get('/', fn() => redirect()->route('login'));

// Route untuk Autentikasi (Login, Logout, Register, Lupa Password)
Route::controller(LoginController::class)->middleware('guest')->group(function () {
    Route::get('/login', 'index')->name('login');
    Route::post('/login', 'authenticate')->name('login.post');
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::view('/register', 'auth.register')->middleware('guest')->name('register');
Route::view('/forgot-password', 'auth.forgot-password')->middleware('guest')->name('password.request');


// Route untuk Fitur Utama Pengguna (yang sudah login)
Route::middleware('auth')->group(function () {
    
    Route::get('/dashboard', function () {
        return view('users.dashboard', ['title' => 'Dashboard']);
    })->name('dashboard');

    // Absensi
    Route::get('/absen', [AbsenController::class, 'absen'])->name('absen');
    Route::post('/absen', [AbsenController::class, 'store'])->name('absen.store');

    // Cuti
    Route::get('/cuti', [CutiController::class, 'create'])->name('cuti');
    Route::post('/cuti', [CutiController::class, 'store'])->name('cuti.store');

    // Fitur Lainnya
    Route::get('/pengajuan-dana', [PengajuanDanaController::class, 'pengajuan_dana'])->name('pengajuan_dana');
    Route::get('/pengajuan-dokumen', [PengajuanDokumenController::class, 'pengajuan_dokumen'])->name('pengajuan_dokumen');
    Route::get('/email', [EmailController::class, 'email'])->name('email');

});


// Route khusus untuk Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Arahkan URL utama admin ke halaman karyawan
    Route::get('/', fn() => redirect()->route('admin.employees.index'));

    // Rute untuk menampilkan halaman (ini yang diubah)
    Route::get('/employees', [UserController::class, 'indexEmployees'])->name('employees.index');
    Route::get('/admins', [UserController::class, 'indexAdmins'])->name('admins.index');
    
    // Rute lama /dashboard, kita arahkan juga ke halaman karyawan
    Route::get('/dashboard', fn() => redirect()->route('admin.employees.index'))->name('dashboard');

    // Resource untuk proses simpan, update, hapus (ini tetap sama)
    Route::resource('users', UserController::class)->except(['index', 'show']);
});