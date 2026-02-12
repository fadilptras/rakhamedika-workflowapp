<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cuti extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'jenis_cuti',
        'tanggal_mulai',
        'tanggal_selesai',
        'alasan',
        'lampiran',
        'status',
        // 'catatan_approval', // Kolom baru
        // 'approver_1_id',
        // 'approver_2_id',
        // 'status_appr_1',
        // 'status_appr_2'
        'status_approver_1',
        'status_approver_2',
        'catatan_approval',
        'tanggal_approve_1',
        'tanggal_approve_2',
    ];

    /**
     * Relasi ke model User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}