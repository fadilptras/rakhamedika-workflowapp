<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'area',
        'pic',
        'nama_user',
        'nama_perusahaan',
        'tanggal_berdiri',
        'email',
        'no_telpon',
        'alamat',
    ];

    // Opsional: Agar otomatis dianggap tanggal oleh Laravel
    protected $casts = [
        'tanggal_berdiri' => 'date',
    ];

    public function interactions()
    {
        return $this->hasMany(Interaction::class)->orderBy('tanggal_interaksi', 'desc');
    }

    public function getTotalKontribusiAttribute()
    {
        // Pastikan relasi diload dulu agar irit query
        if (!$this->relationLoaded('interactions')) {
            $this->load('interactions');
        }

        // Gunakan $this->interactions (Collection) bukan $this->interactions() (Query Builder)
        // IN (Pemasukan)
        $pemasukan = $this->interactions
                          ->where('jenis_transaksi', 'IN') 
                          ->sum('nilai_kontribusi');
        
        // OUT (Pengeluaran)
        $pengeluaran = $this->interactions
                            ->where('jenis_transaksi', 'OUT') 
                            ->sum('nilai_kontribusi');

        return $pemasukan - $pengeluaran;
    }
}