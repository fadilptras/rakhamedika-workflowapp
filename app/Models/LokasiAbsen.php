<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LokasiAbsen extends Model
{
    use HasFactory;
    
    protected $table = 'lokasi_absen';
    protected $fillable = [
        'id',
        'nama',
        'latitude',
        'longitude',
        'radius',
    ];
}
