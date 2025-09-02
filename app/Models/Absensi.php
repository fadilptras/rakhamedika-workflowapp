<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;
    
    protected $table = 'absensi';
    protected $fillable = [
        'user_id',
        'tanggal',
        'jam_masuk',
        'status',
        'keterangan',
        'latitude',
        'longitude',
        'lampiran',
        'jam_keluar',
        'keterangan_keluar',
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