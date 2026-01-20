<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Notifications\Channels\WhatsAppChannel;

class BirthdayNotification extends Notification
{
    use Queueable;

    public $user; // User yang sedang ulang tahun

    /**
     * Buat instance notifikasi baru.
     *
     * @param \App\Models\User $user Orang yang sedang ulang tahun
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function via(object $notifiable): array
    {
        return ['database', WhatsAppChannel::class];
    }

    /**
     * Helper: Membuat Link WhatsApp (wa.me)
     */
    private function getWhatsAppLink($name)
    {
        $noHp = $this->user->no_telepon; 

        if (!$noHp) {
            return null;
        }

        $noHp = preg_replace('/[^0-9]/', '', $noHp);

        if (substr($noHp, 0, 1) === '0') {
            $noHp = '62' . substr($noHp, 1);
        }

        $text = urlencode("Happy Birthday {$name}! 脂 Semoga panjang umur, sehat selalu, dan makin sukses ya!");
        return "https://wa.me/{$noHp}?text={$text}";
    }

    /**
     * Format pesan untuk WhatsApp
     */
    public function toWhatsApp($notifiable)
    {
        // yg ultah
        if ($notifiable->id === $this->user->id) {
            return [
                'message' => "Halo *{$notifiable->name}*,\n\n" .
                             "*SELAMAT ULANG TAHUN!*\n\n" .
                             "Selamat bertambah usia! Semoga sehat selalu, bahagia, dan makin sukses perjalanan karirnya bareng kita.\n\n" .
                             "Nikmati hari spesialmu ya!\n\n"
            ];
        }

        // karyawan lain
        $yangUltah = $this->user->name;
        
        $waLink = $this->getWhatsAppLink($yangUltah);

        $pesan = "*HARI INI ADA YANG ULANG TAHUN!*\n\n" .
                 "Hari ini adalah hari spesial untuk rekan kita: *{$yangUltah}*\n\n" .
                 "Jangan lupa berikan ucapan selamat dan doa terbaik untuk *{$yangUltah}* ya!";

        if ($waLink) {
            $pesan .= "\n\nKlik link ini buat kirim ucapan langsung:\n{$waLink}";
        } else {
            $pesan .= "\n\n(Nomor WhatsApp rekan tidak tersedia)";
        }

        return ['message' => $pesan];
    }

    /**
     * Format pesan untuk Notifikasi Database (Web)
     */
    public function toArray(object $notifiable): array
    {

        if ($notifiable->id === $this->user->id) {
            return [
                'id'      => $this->user->id, 
                'title'   => 'Selamat Ulang Tahun!',
                'message' => "Selamat ulang tahun {$notifiable->name}, semoga hari ini menyenangkan!",
                'url'     => '#', 
                'icon'    => 'fas fa-birthday-cake',
                'color'   => 'text-pink-500',
            ];
        }

        $yangUltah = $this->user->name;
        $waLink    = $this->getWhatsAppLink($yangUltah);

        return [
            'id'      => $this->user->id,
            'title'   => 'Hari Ini Ada yang Ulang Tahun!',
            'message' => "Hari ini {$yangUltah} ulang tahun. Klik untuk kirim ucapan!",
            'url'     => $waLink ?? '#', 
            'icon'    => 'fas fa-birthday-cake',
            'color'   => 'text-pink-500',
        ];
    }
}