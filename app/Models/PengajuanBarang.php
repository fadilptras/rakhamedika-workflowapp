<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanBarang extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_barang';
    protected $fillable = [
    'user_id',
    'judul_pengajuan',
    'divisi',
    'rincian_barang',
    'lampiran',
    'status_atasan',
    'catatan_atasan',
    'atasan_approved_at', 
    'status_gudang',      
    'catatan_gudang',    
    'gudang_approved_at', 
    'status',
    'atasan_id',
    'gudang_id',
];

    protected $casts = [
        'rincian_barang' => 'array',
        'lampiran' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approverAtasan()
    {
        return $this->belongsTo(User::class, 'atasan_id');
    }

    public function approverGudang()
    {
        return $this->belongsTo(User::class, 'gudang_id');
    }
}
