<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanDana extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_dana';
    protected $fillable = [
        'user_id',
        'judul_pengajuan',
        'divisi',
        'nama_bank',
        'no_rekening',
        'total_dana',
        'rincian_dana',
        'lampiran',
        'status',
        'status_atasan',
        'catatan_atasan',
        'status_finance',
        'catatan_finance',
        'status_direktur',
        'catatan_direktur',
        'created_at',
        'updated_at',
        'invoice',
        'bukti_transfer',
        'atasan_id',
        'direktur_id',
        'finance_id',
        
        // =================== TAMBAHAN UNTUK TANGGAL PERSETUJUAN ===================
        'atasan_approved_at',
        'direktur_approved_at',
        'finance_approved_at',
        // ========================================================================
    ];

    protected $casts = [
        'rincian_dana' => 'array',
        'lampiran' => 'array',

        // =================== TAMBAHAN UNTUK CASTING TANGGAL ===================
        'atasan_approved_at' => 'datetime',
        'direktur_approved_at' => 'datetime',
        'finance_approved_at' => 'datetime',
        // ====================================================================
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function atasanApprover()
    {
        return $this->belongsTo(User::class, 'atasan_id');
    }

    public function direkturApprover()
    {
        return $this->belongsTo(User::class, 'direktur_id');
    }

    public function financeApprover()
    {
        return $this->belongsTo(User::class, 'finance_id');
    }
}