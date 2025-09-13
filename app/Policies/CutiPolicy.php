<?php

namespace App\Policies;

use App\Models\Cuti;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Carbon\Carbon;

class CutiPolicy
{
    /**
     * Tentukan apakah user dapat melihat detail cuti.
     */
    public function view(User $user, Cuti $cuti): bool
    {
        // User dapat melihat jika dia adalah pemilik, approver, atau HRD.
        $approver = $this->getApprover($cuti->user);
        return $user->id === $cuti->user_id || $user->jabatan === 'HRD' || ($approver && $user->id === $approver->id);
    }

    /**
     * Tentukan apakah user (approver) dapat memperbarui status.
     */
    public function update(User $user, Cuti $cuti): bool
    {
        $approver = $this->getApprover($cuti->user);
        return $approver && $user->id === $approver->id;
    }

    /**
     * Tentukan apakah user (pemilik) dapat membatalkan cuti.
     */
    public function cancel(User $user, Cuti $cuti): bool
    {
        // Hanya bisa dibatalkan jika:
        // 1. Dia adalah pemiliknya.
        // 2. Statusnya 'disetujui'.
        // 3. Tanggal cuti belum dimulai.
        return $user->id === $cuti->user_id &&
               $cuti->status === 'disetujui' &&
               Carbon::parse($cuti->tanggal_mulai)->isFuture();
    }

    /**
     * Helper privat untuk mendapatkan approver (logika yang sama seperti di Controller).
     */
    private function getApprover(User $user): ?User
    {
        if ($user->jabatan === 'Direktur') return null;
        if (str_starts_with($user->jabatan, 'Kepala')) return User::where('jabatan', 'Direktur')->first();
        if ($user->divisi) {
            $approver = User::where('divisi', $user->divisi)
                            ->where('jabatan', 'like', 'Kepala%')
                            ->where('id', '!=', $user->id)
                            ->first();
            if ($approver) return $approver;
        }
        return User::where('jabatan', 'Direktur')->first();
    }
}