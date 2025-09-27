<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\PengajuanDana;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_picture',
        'jabatan',
        'tanggal_bergabung',
        'divisi',
        'is_kepala_divisi',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'tanggal_bergabung' => 'date:Y-m-d',
        ];
    }
    
    /**
     * Get the fund requests for the user.
     */
    public function pengajuanDanas(): HasMany
    {
        return $this->hasMany(PengajuanDana::class, 'user_id');
    }

    public function absensis(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }

    public function cutis(): HasMany
    {
        return $this->hasMany(Cuti::class);
    }
    
    // --- TAMBAHAN BARU: RELASI KE TABEL LEMBUR ---
    public function lemburs(): HasMany
    {
        return $this->hasMany(Lembur::class);
    }

    public function invitedAgendas()
    {
        return $this->belongsToMany(Agenda::class, 'agenda_user', 'user_id', 'agenda_id');
    }

    public function pengajuanDokumens(): HasMany
    {
        return $this->hasMany(PengajuanDokumen::class);
    }
}