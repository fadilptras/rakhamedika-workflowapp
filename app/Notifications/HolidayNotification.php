<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Holiday;
use App\Notifications\Channels\WhatsAppChannel;

class HolidayNotification extends Notification
{
    use Queueable;
    public $holiday;
    public function __construct(Holiday $holiday)
    {
        $this->holiday = $holiday;
    }
    public function via($notifiable)
    {
        return ['database', WhatsAppChannel::class];
    }

    public function toWhatsApp($notifiable)
    {
        $namaLibur = $this->holiday->keterangan ?? 'Hari Libur Nasional';

        return [
            'message' => "*INFORMASI HARI LIBUR*\n\n" .
                         "Mengingatkan bahwa hari ini kantor libur dalam rangka: *{$namaLibur}*\n" .
                         "Selamat beristirahat dan menikmati waktu luang! Sampai jumpa di hari kerja berikutnya.\n\n"
        ];
    }

    public function toArray($notifiable)
    {
        $namaLibur = $this->holiday->keterangan ?? 'Hari Libur';
        
        return [
            'title'   => 'Hari Ini Libur!',
            'message' => "Hari ini libur: {$namaLibur}. Selamat beristirahat!",
            'url'     => '#',
            'icon'    => 'fas fa-umbrella-beach', 
            'color'   => 'text-green-500',
        ];
    }
}   