<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\Cuti;

class CutiNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $cuti;

    public function __construct(Cuti $cuti)
    {
        $this->cuti = $cuti;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'id' => $this->cuti->id,
            'title' => 'Pengajuan Cuti Baru',
            'message' => 'Pengajuan cuti dari ' . $this->cuti->user->name . ' telah diajukan dan menunggu persetujuan Anda.',
            'url' => route('cuti.show', $this->cuti->id),
            'icon' => 'fas fa-envelope',
            'color' => 'text-yellow-500',
        ];
    }
}