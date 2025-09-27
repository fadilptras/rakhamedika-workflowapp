<?php

namespace App\Policies;

use App\Models\PengajuanDana;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PengajuanDanaPolicy
{
    /**
     * Menentukan apakah user yang sedang login boleh menyetujui (approve/reject) 
     * sebuah pengajuan dana.
     */
    public function approve(User $user, PengajuanDana $pengajuanDana): bool
    {
        // Aturan 1: User adalah Kepala Divisi dari divisi yang mengajukan, DAN
        // status pengajuan dari atasan masih 'menunggu'.
        if ($user->is_kepala_divisi 
            && $user->divisi === $pengajuanDana->user->divisi 
            && $pengajuanDana->status_atasan === 'menunggu') {
            return true;
        }

        // =======================================================================
        // LOGIKA BARU SESUAI IDE ANDA: Mencari Kepala Finance secara dinamis
        // =======================================================================
        // 1. Cari siapa user yang merupakan kepala dari divisi "Finance dan Gudang"
        $kepalaFinance = User::where('divisi', 'Finance dan Gudang')
                             ->where('is_kepala_divisi', true)
                             ->first();

        // 2. Terapkan Aturan:
        // User boleh approve JIKA:
        // - Seorang Kepala Finance berhasil ditemukan, DAN
        // - ID user yang login SAMA DENGAN ID Kepala Finance yang ditemukan, DAN
        // - Status dari Kepala Divisi sebelumnya sudah 'disetujui', DAN
        // - Status dari Finance sendiri masih 'menunggu' atau belum diisi (null).
        if ($kepalaFinance 
            && $user->id === $kepalaFinance->id
            && $pengajuanDana->status_atasan === 'disetujui' 
            && ($pengajuanDana->status_finance === 'menunggu' || $pengajuanDana->status_finance === null)) {
            return true;
        }

        // Jika tidak ada aturan yang cocok, maka tidak diizinkan.
        return false;
    }
}