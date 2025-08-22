<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika tidak mengikuti konvensi Laravel (absensis)
    protected $table = 'absensi';

    // Kolom yang boleh diisi secara massal (mass assignable)
    protected $fillable = [
        'user_id',
        'tanggal',
        'jam_masuk',
        'status',
        'keterangan',
        'lampiran',
    ];

    /**
     * Relasi ke model User.
     * Setiap record absensi dimiliki oleh satu User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}