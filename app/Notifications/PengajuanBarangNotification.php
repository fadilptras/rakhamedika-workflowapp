<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\PengajuanBarang;

class PengajuanBarangNotification extends Notification
{
    use Queueable;

    public $pengajuanBarang;
    public $tipe;

    public function __construct(PengajuanBarang $pengajuanBarang, string $tipe = 'baru')
    {
        $this->pengajuanBarang = $pengajuanBarang;
        $this->tipe = $tipe;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $title = '';
        $message = '';
        $icon = 'fas fa-box';
        $color = 'text-blue-600';
        $pemohon = $this->pengajuanBarang->user->name;
        $judulPengajuan = \Illuminate\Support\Str::limit($this->pengajuanBarang->judul_pengajuan, 30);

        switch ($this->tipe) {
            case 'disetujui_atasan':
                $title = 'Pengajuan Barang Diproses';
                $message = "Pengajuan '$judulPengajuan' Anda telah disetujui atasan dan diteruskan ke Gudang.";
                $icon = 'fas fa-check-double';
                $color = 'text-green-500';
                break;
            case 'disetujui_gudang':
                $title = 'Pengajuan Barang Disetujui';
                $message = "Kabar baik! Pengajuan barang '$judulPengajuan' Anda telah disetujui oleh Gudang.";
                $icon = 'fas fa-check-circle';
                $color = 'text-green-600';
                break;
            case 'ditolak':
                $title = 'Pengajuan Barang Ditolak';
                $message = "Mohon maaf, pengajuan barang '$judulPengajuan' Anda ditolak.";
                $icon = 'fas fa-times-circle';
                $color = 'text-red-600';
                break;
            case 'baru':
            default:
                $title = 'Pengajuan Barang Baru';
                $message = "$pemohon mengajukan barang baru: '$judulPengajuan'. Mohon direview.";
                break;
        }

        return [
            'id' => $this->pengajuanBarang->id,
            'title' => $title,
            'message' => $message,
            'url' => route('pengajuan_barang.show', $this->pengajuanBarang->id),
            'icon' => $icon,
            'color' => $color,
        ];
    }
}
