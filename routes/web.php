<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AbsenController;
use App\Http\Controllers\PengajuanDanaController;
use App\Http\Controllers\PengajuanDokumenController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\Admin\AbsensiController;
use App\Http\Controllers\Admin\CutiController as AdminCutiController;
use App\Http\Controllers\RekapAbsenController;
use App\Http\Controllers\Admin\AdminPengajuanDanaController;
use App\Http\Controllers\Admin\LokasiAbsenController;
use App\Http\Controllers\Admin\AdminLemburController; // <-- Tambahkan ini
use App\Http\Controllers\ProfileController;

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

    // --- TAMBAHAN BARU UNTUK FITUR LEMBUR ---
    Route::post('/absen/lembur', [AbsenController::class, 'storeLembur'])->name('absen.lembur.store');
    Route::patch('/absen/lembur/keluar/{lembur}', [AbsenController::class, 'updateLemburKeluar'])->name('absen.lembur.keluar');
    // --- AKHIR TAMBAHAN ---

    // Cuti
    Route::get('/cuti', [CutiController::class, 'create'])->name('cuti');
    Route::post('/cuti', [CutiController::class, 'store'])->name('cuti.store');
    // --- Rute Baru untuk Cuti ---
    Route::get('/cuti/{cuti}', [CutiController::class, 'show'])->name('cuti.show');
    Route::match(['PUT', 'PATCH'], '/cuti/{cuti}/status', [CutiController::class, 'updateStatus'])->name('cuti.updateStatus');
    Route::post('/cuti/{cuti}/cancel', [CutiController::class, 'cancel'])->name('cuti.cancel');
    // Fitur Lainnya
    
    // Notifikasi
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    
    Route::get('/profile', [ProfileController::class, 'editProfile'])->name('profil.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profil.update');
    
    // Pengajuan Dana
    Route::get('/pengajuan-dana', [PengajuanDanaController::class, 'index'])->name('pengajuan_dana.index');
    Route::post('/pengajuan-dana', [PengajuanDanaController::class, 'store'])->name('pengajuan_dana.store');
    Route::get('/pengajuan-dana/{pengajuanDana}', [PengajuanDanaController::class, 'show'])->name('pengajuan_dana.show');
    Route::post('/pengajuan-dana/{pengajuanDana}/approve', [PengajuanDanaController::class, 'approve'])->name('pengajuan_dana.approve');
    Route::post('/pengajuan-dana/{pengajuanDana}/reject', [PengajuanDanaController::class, 'reject'])->name('pengajuan_dana.reject');

    Route::get('/pengajuan-dokumen', [PengajuanDokumenController::class, 'pengajuan_dokumen'])->name('pengajuan_dokumen');
    
    // Rekap Absensi Karyawan
    Route::get('/rekap-absen', [RekapAbsenController::class, 'index'])->name('rekap_absen.index');

});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/', fn() => redirect()->route('admin.employees.index'));
    
     // Rute untuk Rekap Absensi
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::get('/absensi/pdf', [AbsensiController::class, 'downloadPDF'])->name('absensi.pdf');

    // Rute untuk mengelola KARYAWAN (role='user')
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [UserController::class, 'indexByRole'])->defaults('role', 'user')->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::post('/update', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    // Rute untuk mengelola ADMIN (role='admin')
    Route::prefix('admins')->name('admins.')->group(function () {
        Route::get('/', [UserController::class, 'indexByRole'])->defaults('role', 'admin')->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::post('/update', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    // Manajemen Cuti
    Route::get('/cuti', [AdminCutiController::class, 'index'])->name('cuti.index');
    Route::get('/cuti/{cuti}', [AdminCutiController::class, 'show'])->name('cuti.show');
    Route::patch('/cuti/{cuti}', [AdminCutiController::class, 'updateStatus'])->name('cuti.update');

    // Rekap Pengajuan Dana
    Route::prefix('pengajuan-dana')->name('pengajuan_dana.')->group(function () {
        Route::get('/', [AdminPengajuanDanaController::class, 'index'])->name('index');
        Route::get('/{pengajuanDana}', [AdminPengajuanDanaController::class, 'show'])->name('show');
        Route::post('/{pengajuanDana}/approve', [AdminPengajuanDanaController::class, 'approve'])->name('approve');
        Route::post('/{pengajuanDana}/reject', [AdminPengajuanDanaController::class, 'reject'])->name('reject');
    });
    
    Route::get('/lembur', [AdminLemburController::class, 'index'])->name('lembur.index');
});
