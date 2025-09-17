<?php

namespace App\Policies;

use App\Models\Cuti;
use App\Models\User;
use App\Http\Controllers\CutiController;
use Illuminate\Auth\Access\Response;
use Carbon\Carbon;

class CutiPolicy
{
    /**
     * Tentukan apakah user dapat melihat detail cuti.
     */
    public function view(User $user, Cuti $cuti): bool
    {
        // User dapat melihat jika dia adalah pemiliknya
        if ($user->id === $cuti->user_id) {
            return true;
        }

        // Cek apakah user adalah atasan yang berhak menyetujui
        $approver = $this->getApprover($cuti->user);
        if ($approver && $user->id === $approver->id) {
            return true;
        }
        
        // Izinkan HRD melihat semua pengajuan
        if ($user->jabatan === 'HRD') {
            return true;
        }

        return false;
    }

    /**
     * Helper privat untuk mendapatkan approver (logika yang sama seperti di Controller).
     */
    private function getApprover(User $user): ?User
    {
        if ($user->jabatan === 'Direktur') {
            return null;
        }

        if (str_starts_with($user->jabatan, 'Kepala')) {
            return User::where('jabatan', 'Direktur')->first();
        }
        
        if ($user->divisi) {
            $approver = User::where('divisi', $user->divisi)
                            ->where('jabatan', 'like', 'Kepala%')
                            ->where('id', '!=', $user->id)
                            ->first();
            if ($approver) {
                return $approver;
            }
        }

        return User::where('jabatan', 'Direktur')->first();
    }
}