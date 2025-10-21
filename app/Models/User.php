<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\PengajuanDana;
use App\Models\RiwayatPendidikan;
use App\Models\RiwayatPekerjaan;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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
        'nomor_telepon',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'nik',
        'kontak_darurat_nama',
        'kontak_darurat_nomor',

        'nip',
        'status_karyawan',
        'atasan_id',
        'lokasi_kerja',
        'tanggal_mulai_kontrak',
        'tanggal_akhir_kontrak',
        'tanggal_berhenti',
        'agama',
        'golongan_darah',
        'status_pernikahan',
        'alamat_ktp', // Ini pengganti 'alamat'
        'alamat_domisili',
        'kontak_darurat_hubungan',
        'npwp',
        'ptkp',
        'bpjs_kesehatan',
        'bpjs_ketenagakerjaan',
        'nama_bank',
        'nomor_rekening',
        'pemilik_rekening',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'tanggal_bergabung' => 'date:Y-m-d',
            'tanggal_lahir'     => 'date:Y-m-d',
            // --- TAMBAHKAN INI ---
            'tanggal_mulai_kontrak' => 'date:Y-m-d',
            'tanggal_akhir_kontrak' => 'date:Y-m-d',
            'tanggal_berhenti'      => 'date:Y-m-d',
        ];
    }
    
    // --- (Relasi lama Anda tetap di sini) ---
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


    /**
     * Relasi ke Riwayat Pendidikan.
     */
    public function riwayatPendidikan(): HasMany
    {
        return $this->hasMany(RiwayatPendidikan::class, 'user_id');
    }

    /**
     * Relasi ke Riwayat Pekerjaan.
     */
    public function riwayatPekerjaan(): HasMany
    {
        return $this->hasMany(RiwayatPekerjaan::class, 'user_id');
    }
}