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
        'total_hari',
        'alasan',
        'lampiran',
        'status',
        'approver_cuti_1_id', 'status_approver_1', 'catatan_approver_1', 'tanggal_approve_1',
        'approver_cuti_2_id', 'status_approver_2', 'catatan_approver_2', 'tanggal_approve_2',
        'approver_cuti_3_id', 'status_approver_3', 'catatan_approver_3', 'tanggal_approve_3',
    ];

    /**
     * Relasi ke model User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver1() {
        return $this->belongsTo(User::class, 'approver_cuti_1_id');
    }

    public function approver2() {
        return $this->belongsTo(User::class, 'approver_cuti_2_id');
    }

    public function approver3() {
        return $this->belongsTo(User::class, 'approver_cuti_3_id');
    }
}