<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\User; // Import Model User

class BirthdayNotification extends Notification
{
    use Queueable;

    public $user; // User yang sedang ulang tahun

    /**
     * Buat instance notifikasi baru.
     *
     * @param \App\Models\User $user Orang yang sedang ulang tahun
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
        // Tentukan Pesan
        $namaYangUltah = $this->user->name;
        
        // Gunakan icon kue ulang tahun
        $icon = 'fas fa-birthday-cake'; 
        
        // Warna pink/ungu agar terlihat festive, sesuai pola Tailwind di file lain
        $color = 'text-pink-500'; 

        return [
            // Kita kirim ID user yang ultah, agar frontend bisa handle jika perlu
            'id' => $this->user->id, 
            
            'title' => 'Hari Ini Ada yang Ulang Tahun! 🎉',
            
            'message' => "Hari ini adalah ulang tahun $namaYangUltah. Jangan lupa ucapkan selamat dan doa terbaik!",
            
            // Arahkan ke profile user tersebut (Asumsi route 'profile.show' atau sejenis ada)
            // Jika tidak ada route khusus user, bisa ganti jadi url('#') atau route('dashboard')
            // Saya gunakan '#' aman sementara ini
            'url' => '#', 
            
            'icon' => $icon,
            'color' => $color,
        ];
    }
}