<?php

namespace App\Policies;

use App\Models\PengajuanDana;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PengajuanDanaPolicy
{
    public function view(User $user, PengajuanDana $pengajuanDana): bool
    {
        if ($user->id === $pengajuanDana->user_id || $user->jabatan === 'HRD') {
            return true;
        }
        $directApprover = $this->getApprover($pengajuanDana->user);
        if ($directApprover && $user->id === $directApprover->id) {
            return true;
        }
        $financeHead = User::where('divisi', 'Finance dan Gudang')->where('is_kepala_divisi', true)->first();
        if ($financeHead && $user->id === $financeHead->id) {
            return true;
        }
        return false;
    }

    public function approve(User $user, PengajuanDana $pengajuanDana): bool
    {
        $pemohon = $pengajuanDana->user;
        if ($pemohon->is_kepala_divisi) {
            $direktur = User::where('jabatan', 'Direktur')->first();
            if ($direktur && $user->id === $direktur->id && $pengajuanDana->status_direktur === 'menunggu') {
                return true;
            }
        } 
        else {
            $atasanPemohon = $this->getApprover($pemohon);
            if ($atasanPemohon && $user->id === $atasanPemohon->id && $pengajuanDana->status_atasan === 'menunggu') {
                return true;
            }
        }
        $kepalaFinance = User::where('divisi', 'Finance dan Gudang')->where('is_kepala_divisi', true)->first();
        if ($kepalaFinance && $user->id === $kepalaFinance->id) {
            $atasanApproved = $pengajuanDana->status_atasan === 'disetujui';
            $direkturApproved = $pengajuanDana->status_direktur === 'disetujui';
            $financeWaiting = $pengajuanDana->status_finance === 'menunggu' || $pengajuanDana->status_finance === null;
            if (($atasanApproved || $direkturApproved) && $financeWaiting) {
                return true;
            }
        }
        return false;
    }

    // =================== METHOD BARU DITAMBAHKAN DI SINI ===================
    /**
     * Determine whether the user can cancel the pengajuan dana.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PengajuanDana  $pengajuanDana
     * @return bool
     */
    public function cancel(User $user, PengajuanDana $pengajuanDana): bool
    {
        // Aturan:
        // 1. Pengguna harus menjadi pemilik pengajuan.
        // 2. Status pengajuan harus 'diajukan' ATAU 'diproses'.
        return $user->id === $pengajuanDana->user_id &&
               in_array($pengajuanDana->status, ['diajukan', 'diproses']);
    }
    // =======================================================================

    public function uploadBuktiTransfer(User $user, PengajuanDana $pengajuanDana): bool
    {
        $kepalaFinance = User::where('jabatan', 'Kepala Finance')->first();
        return $kepalaFinance 
                && $user->id === $kepalaFinance->id 
                && $pengajuanDana->status === 'disetujui'
                && is_null($pengajuanDana->bukti_transfer);
    }

    public function uploadFinalInvoice(User $user, PengajuanDana $pengajuanDana): bool
    {
        $isOwner = $user->id === $pengajuanDana->user_id;
        $isApproved = $pengajuanDana->status === 'disetujui';
        $transferUploaded = !is_null($pengajuanDana->bukti_transfer);
        $invoiceNotYetUploaded = is_null($pengajuanDana->invoice);
        return $isOwner && $isApproved && $transferUploaded && $invoiceNotYetUploaded;
    }

    private function getApprover(User $user): ?User
    {
        if ($user->jabatan === 'Direktur') return null;
        if ($user->is_kepala_divisi) return User::where('jabatan', 'Direktur')->first();
        if ($user->divisi) {
            $approver = User::where('divisi', $user->divisi)
                            ->where('is_kepala_divisi', true)
                            ->where('id', '!=', $user->id)
                            ->first();
            if ($approver) return $approver;
        }
        return User::where('jabatan', 'Direktur')->first();
    }
}