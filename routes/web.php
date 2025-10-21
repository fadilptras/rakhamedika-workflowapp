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
use App\Http\Controllers\Admin\AdminLemburController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\Admin\AdminPengajuanDokumenController;
use App\Http\Controllers\Admin\AdminAgendaController;
use App\Http\Controllers\CrmController;

// Route utama, langsung arahkan ke halaman login
Route::get('/', fn() => redirect()->route('login'));

// Route untuk Autentikasi (Login, Logout, Register, Lupa Password)
Route::controller(LoginController::class)->middleware('guest')->group(function () {
    Route::get('/login', 'index')->name('login');
    Route::post('/login', 'authenticate')->name('login.post');
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

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
    Route::get('/cuti', [CutiController::class, 'create'])->name('cuti.create'); // Nama diubah dari 'cuti' menjadi 'cuti.create'
    Route::post('/cuti', [CutiController::class, 'store'])->name('cuti.store');
    Route::get('/cuti/{cuti}', [CutiController::class, 'show'])->name('cuti.show');
    Route::match(['PUT', 'PATCH'], '/cuti/{cuti}/status', [CutiController::class, 'updateStatus'])->name('cuti.updateStatus');
    Route::post('/cuti/{cuti}/cancel', [CutiController::class, 'cancel'])->name('cuti.cancel');
    
    // Notifikasi
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    
    Route::get('/profile', [ProfileController::class, 'editProfile'])->name('profil.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profil.update');
    Route::post('/profile/check-password', [ProfileController::class, 'checkCurrentPassword'])->name('profile.checkPassword');
    Route::get('/profile/download-pdf', [ProfileController::class, 'downloadPdf'])->name('profile.downloadPdf');

    // Pengajuan Dana
    Route::get('/pengajuan-dana', [PengajuanDanaController::class, 'index'])->name('pengajuan_dana.index');
    Route::post('/pengajuan-dana', [PengajuanDanaController::class, 'store'])->name('pengajuan_dana.store');
    Route::get('/pengajuan-dana/{pengajuanDana}', [PengajuanDanaController::class, 'show'])->name('pengajuan_dana.show');
    Route::post('/pengajuan-dana/{pengajuanDana}/approve', [PengajuanDanaController::class, 'approve'])->name('pengajuan_dana.approve');
    Route::post('/pengajuan-dana/{pengajuanDana}/reject', [PengajuanDanaController::class, 'reject'])->name('pengajuan_dana.reject');
    // --- PERUBAHAN DI SINI ---
    // Mengubah nama route untuk lebih jelas (bukti transfer oleh finance)
    Route::post('/pengajuan-dana/{pengajuanDana}/upload-bukti-transfer', [PengajuanDanaController::class, 'uploadBuktiTransfer'])
        ->name('pengajuan_dana.upload_bukti_transfer');

    // --- TAMBAHAN BARU DI SINI ---
    // Route baru untuk upload invoice final oleh pemohon
    Route::post('/pengajuan-dana/{pengajuanDana}/upload-final-invoice', [PengajuanDanaController::class, 'uploadFinalInvoice'])
        ->name('pengajuan_dana.upload_final_invoice');
    Route::get('/pengajuan-dana/{pengajuanDana}/download', [PengajuanDanaController::class, 'downloadPDF'])->name('pengajuan_dana.download');
    Route::post('/pengajuan-dana/{pengajuanDana}/cancel', [PengajuanDanaController::class, 'cancel'])->name('pengajuan_dana.cancel');

    // Di dalam Route::middleware('auth')->group(...)
    Route::get('/pengajuan-dokumen', [PengajuanDokumenController::class, 'index'])->name('pengajuan_dokumen.index');
    Route::post('/pengajuan-dokumen', [PengajuanDokumenController::class, 'store'])->name('pengajuan_dokumen.store');
    Route::get('/pengajuan-dokumen/{dokumen}/download', [PengajuanDokumenController::class, 'download'])->name('pengajuan_dokumen.download');
    

    // Rekap Absensi Karyawan
    Route::get('/rekap-absen', [RekapAbsenController::class, 'index'])->name('rekap_absen.index');

    // Route untuk fitur Agenda/Kalender
    Route::get('/agendas', [AgendaController::class, 'index'])->name('agendas.index');
    Route::post('/agendas', [AgendaController::class, 'store'])->name('agendas.store');
    Route::get('/get-users', [AgendaController::class, 'getUsers'])->name('agendas.getUsers');
    Route::put('/agendas/{agenda}', [AgendaController::class, 'update'])->name('agendas.update');
    Route::delete('/agendas/{agenda}', [AgendaController::class, 'destroy'])->name('agendas.destroy');

    Route::get('/crm', [CrmController::class, 'index'])->name('crm.index');
    Route::get('/crm/create', [CrmController::class, 'create'])->name('crm.create');
    Route::get('/crm/detail', [CrmController::class, 'show'])->name('crm.show'); // Menggunakan /detail sementara
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/', fn() => redirect()->route('admin.employees.index'));
    
    // Rute untuk mengelola KARYAWAN (role='user')
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [UserController::class, 'indexByRole'])->defaults('role', 'user')->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::post('/update', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');

        // --- RUTE BARU UNTUK SET KEPALA DIVISI ---
        Route::post('/{user}/set-as-head', [UserController::class, 'setAsDivisionHead'])->name('setAsHead');
        Route::get('/{user}/download-pdf', [UserController::class, 'downloadProfilePdf'])->name('downloadProfilePdf');
    });

    // Rute untuk mengelola ADMIN (role='admin')
    Route::prefix('admins')->name('admins.')->group(function () {
        Route::get('/', [UserController::class, 'indexByRole'])->defaults('role', 'admin')->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::post('/update', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });
    
    // Rute untuk menu Kelola Absen
    Route::prefix('absensi')->name('absensi.')->group(function () {
        // Rute untuk Aktivitas Harian & download PDF
        Route::get('/', [AbsensiController::class, 'index'])->name('index');
        Route::get('/pdf/harian', [AbsensiController::class, 'downloadPdfHarian'])->name('downloadPdfHarian');
        
        // Rute untuk Rekap Absensi Bulanan & download PDF
        Route::get('/rekap', [AbsensiController::class, 'rekap'])->name('rekap');
        Route::get('/rekap/pdf', [AbsensiController::class, 'downloadPdf'])->name('rekap.downloadPdf');
        Route::get('/rekap/excel', [AbsensiController::class, 'downloadExcel'])->name('rekap.downloadExcel');
    });


    // Rute untuk Rekap Lembur (terpisah)
    Route::prefix('lembur')->name('lembur.')->group(function () {
        Route::get('/', [AdminLemburController::class, 'index'])->name('index');
        Route::get('/pdf', [AdminLemburController::class, 'downloadPdf'])->name('downloadPdf');
    });
        
    // Manajemen Cuti (HANYA UNTUK MELIHAT)
    Route::prefix('cuti')->name('cuti.')->group(function () {
        Route::get('/', [AdminCutiController::class, 'index'])->name('index');
        Route::get('/pengaturan', [AdminCutiController::class, 'pengaturanCuti'])->name('pengaturan');
        Route::post('/pengaturan', [AdminCutiController::class, 'updatePengaturanCuti'])->name('updatePengaturan');
        Route::get('/{cuti}', [AdminCutiController::class, 'show'])->name('show');
    });

    // == PERBAIKAN DI SINI ==
    // Hapus 'admin/' dari URL dan 'admin.' dari nama rute
    Route::prefix('pengajuan-dana')->name('pengajuan_dana.')->group(function() {
        Route::get('/', [AdminPengajuanDanaController::class, 'index'])->name('index');
        Route::get('/rekap-pdf', [AdminPengajuanDanaController::class, 'downloadRekapPDF'])->name('downloadRekapPdf');
        Route::get('/{pengajuanDana}', [AdminPengajuanDanaController::class, 'show'])->name('show');
        Route::get('/{pengajuanDana}/download', [AdminPengajuanDanaController::class, 'downloadPDF'])->name('downloadPdf');
    });

    Route::prefix('pengajuan-dokumen')->name('pengajuan-dokumen.')->group(function() {
        Route::get('/', [AdminPengajuanDokumenController::class, 'index'])->name('index');
        Route::get('/{dokumen}', [AdminPengajuanDokumenController::class, 'show'])->name('show');
        Route::put('/{dokumen}', [AdminPengajuanDokumenController::class, 'update'])->name('update');
    });

    Route::prefix('agenda')->name('agenda.')->group(function () {
        // [GET] Menampilkan halaman utama kalender dan daftar agenda
        Route::get('/', [AdminAgendaController::class, 'index'])->name('index');

        // [POST] Menyimpan agenda baru dari modal
        Route::post('/', [AdminAgendaController::class, 'store'])->name('store');

        // [GET] Mengambil data semua user untuk ditampilkan di form
        Route::get('/get-all-users', [AdminAgendaController::class, 'getAllUsers'])->name('getAllUsers');

        // [GET] Mengambil data event untuk ditampilkan di FullCalendar
        Route::get('/events', [AdminAgendaController::class, 'getAdminAgendas'])->name('getEvents');

        // [PUT] Mengupdate agenda yang sudah ada
        Route::put('/{agenda}', [AdminAgendaController::class, 'update'])->name('update');

        // [DELETE] Menghapus agenda
        Route::delete('/{agenda}', [AdminAgendaController::class, 'destroy'])->name('destroy');
    });
    
});