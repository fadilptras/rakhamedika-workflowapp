<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\Cuti;

class CutiNotification extends Notification
{
    use Queueable;

    public $cuti;
    public $tipe;

    /**
     * Buat instance notifikasi baru.
     *
     * @param \App\Models\Cuti $cuti
     * @param string $tipe Konteks notifikasi: 'baru', 'disetujui', 'ditolak'
     * @return void
     */
    public function __construct(Cuti $cuti, string $tipe = 'baru')
    {
        $this->cuti = $cuti;
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
        $icon = 'fas fa-envelope';
        $color = 'text-yellow-500';
        $pemohon = $this->cuti->user->name;
        $tanggalMulai = \Carbon\Carbon::parse($this->cuti->tanggal_mulai)->format('d M Y');

        switch ($this->tipe) {
            case 'disetujui':
                $title = 'Pengajuan Cuti Disetujui';
                $message = "Pengajuan cuti Anda untuk tanggal $tanggalMulai telah disetujui. Selamat berlibur!";
                $icon = 'fas fa-check-circle';
                $color = 'text-green-600';
                break;
            case 'ditolak':
                $title = 'Pengajuan Cuti Ditolak';
                $message = "Mohon maaf, pengajuan cuti Anda untuk tanggal $tanggalMulai ditolak.";
                $icon = 'fas fa-times-circle';
                $color = 'text-red-600';
                break;

            case 'dibatalkan':
                $title = 'Pengajuan Cuti Dibatalkan';
                $message = "$pemohon telah membatalkan pengajuan cutinya untuk tanggal $tanggalMulai.";
                $icon = 'fas fa-ban';
                $color = 'text-gray-500';
                break;

            case 'baru':
            default:
                $title = 'Pengajuan Cuti Baru';
                $message = "$pemohon mengajukan cuti baru mulai tanggal $tanggalMulai. Mohon direview.";
                break;
        }

        return [
            'id' => $this->cuti->id,
            'title' => $title,
            'message' => $message,
            'url' => route('cuti.show', $this->cuti->id),
            'icon' => $icon,
            'color' => $color,
        ];
    }
}