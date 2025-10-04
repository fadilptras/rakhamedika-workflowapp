<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\PengajuanDana;

class PengajuanDanaNotification extends Notification
{
    use Queueable;

    public $pengajuanDana;
    public $tipe;

    /**
     * Buat instance notifikasi baru.
     *
     * @param \App\Models\PengajuanDana $pengajuanDana
     * @param string $tipe Konteks notifikasi
     * @return void
     */
    public function __construct(PengajuanDana $pengajuanDana, string $tipe = 'baru')
    {
        $this->pengajuanDana = $pengajuanDana;
        $this->tipe = $tipe;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $title = '';
        $message = '';
        $icon = 'fas fa-coins';
        $color = 'text-blue-600';
        $pemohon = $this->pengajuanDana->user->name;
        $judulPengajuan = \Illuminate\Support\Str::limit($this->pengajuanDana->judul_pengajuan, 30);

        switch ($this->tipe) {
            case 'disetujui_atasan':
                $title = 'Pengajuan Dana Diproses';
                $message = "Pengajuan '$judulPengajuan' Anda telah disetujui atasan dan diteruskan ke Finance.";
                $icon = 'fas fa-check-double';
                $color = 'text-green-500';
                break;
            case 'disetujui_finance':
                $title = 'Pengajuan Dana Disetujui';
                $message = "Kabar baik! Pengajuan dana '$judulPengajuan' Anda telah disetujui oleh Finance.";
                $icon = 'fas fa-check-circle';
                $color = 'text-green-600';
                break;
            case 'ditolak':
                $title = 'Pengajuan Dana Ditolak';
                $message = "Mohon maaf, pengajuan dana '$judulPengajuan' Anda ditolak.";
                $icon = 'fas fa-times-circle';
                $color = 'text-red-600';
                break;
            case 'bukti_transfer':
                $title = 'Dana Telah Ditransfer';
                $message = "Dana untuk pengajuan '$judulPengajuan' telah ditransfer. Silakan cek rekening Anda.";
                $icon = 'fas fa-receipt';
                $color = 'text-indigo-600';
                break;

            // =================== KODE BARU DIMULAI DI SINI ===================
            case 'dibatalkan':
                $title = 'Pengajuan Dibatalkan';
                $message = "Pengajuan dana '$judulPengajuan' oleh $pemohon telah dibatalkan.";
                $icon = 'fas fa-ban'; // Ikon untuk pembatalan
                $color = 'text-slate-500'; // Warna netral
                break;
            // ==================== KODE BARU SELESAI DI SINI ====================

            case 'baru':
            default:
                $title = 'Pengajuan Dana Baru';
                $message = "$pemohon mengajukan dana baru: '$judulPengajuan'. Mohon direview.";
                break;
        }

        return [
            'id' => $this->pengajuanDana->id,
            'title' => $title,
            'message' => $message,
            'url' => route('pengajuan_dana.show', $this->pengajuanDana->id),
            'icon' => $icon,
            'color' => $color,
        ];
    }
}