<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Absensi;
use App\Models\Lembur;
use App\Notifications\Channels\WhatsAppChannel; // Import Channel WA
use Carbon\Carbon;

class AbsensiNotification extends Notification
{
    use Queueable;

    public $data; // Bisa berupa model Absensi atau Lembur
    public $tipe; // 'masuk', 'keluar', 'lembur_masuk', 'lembur_keluar'

    public function __construct($data, string $tipe)
    {
        $this->data = $data;
        $this->tipe = $tipe;
    }

    public function via($notifiable)
    {
        // Kirim ke Database (Lonceng) dan WhatsApp
        return ['database', WhatsAppChannel::class];
    }

    public function toWhatsApp($notifiable)
    {
        $nama = $notifiable->name;
        $waktu = now()->translatedFormat('d F Y H:i');
        
        switch ($this->tipe) {
            case 'masuk':
                $pesan = "Halo {$nama},\n\nTerima kasih telah melakukan *Absen Masuk* pada {$waktu}.\n\nSelamat bekerja dan semoga hari Anda menyenangkan! 💪";
                break;
            case 'keluar':
                $pesan = "Halo {$nama},\n\nAnda telah melakukan *Absen Keluar* pada {$waktu}.\n\nTerima kasih atas kerja keras Anda hari ini. Selamat beristirahat! 🏠";
                break;
            case 'lembur_masuk':
                $pesan = "Halo {$nama},\n\nAnda telah memulai *Lembur* pada {$waktu}.\n\nTetap semangat! ☕";
                break;
            case 'lembur_keluar':
                $pesan = "Halo {$nama},\n\nAnda telah menyelesaikan *Lembur* pada {$waktu}.\n\nHati-hati di jalan dan selamat beristirahat.";
                break;
            default:
                $pesan = "Absensi berhasil dicatat.";
        }

        return ['message' => $pesan];
    }

    public function toArray($notifiable)
    {
        // Data untuk notifikasi di website (lonceng)
        $pesanPendek = '';
        $icon = '';
        $color = '';

        switch ($this->tipe) {
            case 'masuk':
                $pesanPendek = "Absen Masuk berhasil dicatat.";
                $icon = 'fas fa-sign-in-alt';
                $color = 'text-green-500';
                break;
            case 'keluar':
                $pesanPendek = "Absen Keluar berhasil dicatat.";
                $icon = 'fas fa-sign-out-alt';
                $color = 'text-gray-500';
                break;
            case 'lembur_masuk':
                $pesanPendek = "Mulai Lembur dicatat.";
                $icon = 'fas fa-briefcase';
                $color = 'text-orange-500';
                break;
            case 'lembur_keluar':
                $pesanPendek = "Selesai Lembur dicatat.";
                $icon = 'fas fa-home';
                $color = 'text-blue-500';
                break;
        }

        return [
            'title' => 'Info Absensi',
            'message' => $pesanPendek,
            'icon' => $icon,
            'color' => $color,
            'url' => route('absen'), // Arahkan kembali ke halaman absen
        ];
    }
}