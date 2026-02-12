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
        'user_id', 'judul_pengajuan', 'divisi', 'rincian_barang', 'lampiran',
        'status', 'status_appr_1', 'status_appr_2',
        'approver_1_id', 'catatan_approver_1', 'tanggal_approved_1',
        'approver_2_id', 'catatan_approver_2', 'tanggal_approved_2',
    ];

    protected $casts = [
        'rincian_barang' => 'array',
        'lampiran' => 'array',
        'tanggal_approved_1' => 'timestamp', // Sesuai permintaan Anda
        'tanggal_approved_2' => 'timestamp',
    ];

    /**
     * Relasi ke pembuat pengajuan (User)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver1() {
        return $this->belongsTo(User::class, 'approver_1_id');
    }

    public function approver2() {
        return $this->belongsTo(User::class, 'approver_2_id');
    }
}
