<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AbsenController;
use App\Http\Controllers\PengajuanDanaController;
use App\Http\Controllers\PengajuanDokumenController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\Admin\AbsensiController;
use App\Http\Controllers\Admin\CutiController as AdminCutiController;
use App\Http\Controllers\RekapAbsenController;

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

    Route::patch('/absen/keluar/{absensi}', [AbsenController::class, 'updateKeluar'])->name('absen.keluar');

    // Cuti
    Route::get('/cuti', [CutiController::class, 'create'])->name('cuti');
    Route::post('/cuti', [CutiController::class, 'store'])->name('cuti.store');

    // Fitur Lainnya
    
    // Pengajuan Dana
    Route::get('/pengajuan-dana', [PengajuanDanaController::class, 'index'])->name('pengajuan_dana.index');
    Route::post('/pengajuan-dana', [PengajuanDanaController::class, 'store'])->name('pengajuan_dana.store');
    Route::get('/pengajuan-dana/{pengajuanDana}', [PengajuanDanaController::class, 'show'])->name('pengajuan_dana.show'); // Rute untuk halaman detail
    
    Route::get('/pengajuan-dokumen', [PengajuanDokumenController::class, 'pengajuan_dokumen'])->name('pengajuan_dokumen');
    Route::get('/email', [EmailController::class, 'email'])->name('email');
    
    // Rekap Absensi Karyawan
    Route::get('/rekap-absen', [RekapAbsenController::class, 'index'])->name('rekap_absen.index');

});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/', fn() => redirect()->route('admin.employees.index'));
    
     // Rute untuk Rekap Absensi
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::get('/absensi/pdf', [AbsensiController::class, 'downloadPDF'])->name('absensi.pdf'); // <-- TAMBAHKAN INI

    // Rute untuk mengelola KARYAWAN (role='user')
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [UserController::class, 'indexByRole'])->defaults('role', 'user')->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    // Rute untuk mengelola ADMIN (role='admin')
    Route::prefix('admins')->name('admins.')->group(function () {
        Route::get('/', [UserController::class, 'indexByRole'])->defaults('role', 'admin')->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    // Manajemen Cuti
    Route::get('/cuti', [AdminCutiController::class, 'index'])->name('cuti.index');
    Route::patch('/cuti/{cuti}', [AdminCutiController::class, 'updateStatus'])->name('cuti.update');
});
