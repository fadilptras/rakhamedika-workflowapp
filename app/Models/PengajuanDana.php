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
        'status_hrd',
        'catatan_hrd',
        'status_direktur',
        'catatan_direktur',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'rincian_dana' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
