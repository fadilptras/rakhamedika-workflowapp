<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\PengajuanDana; // <-- Import model PengajuanDana

class PengajuanDanaNotification extends Notification
{
    use Queueable;

    public $pengajuanDana;

    public function __construct(PengajuanDana $pengajuanDana)
    {
        $this->pengajuanDana = $pengajuanDana;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'id' => $this->pengajuanDana->id,
            'title' => 'Pengajuan Dana Baru',
            'message' => 'Pengajuan dana dengan judul "' . $this->pengajuanDana->judul_pengajuan . '" telah diajukan.',
            'url' => route('pengajuan_dana.show', $this->pengajuanDana->id),
            'icon' => 'fas fa-coins',
            'color' => 'text-blue-600',
        ];
    }
}