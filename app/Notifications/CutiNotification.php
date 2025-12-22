<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Cuti;
// IMPORT WAJIB CHANNEL
use App\Notifications\Channels\FirebaseChannel;
use App\Notifications\Channels\WhatsAppChannel;
use Carbon\Carbon;
use Kreait\Laravel\Firebase\Messages\FirebaseMessage;

class CutiNotification extends Notification
{
    use Queueable;

    public $cuti;
    public $tipe;

    public function __construct(Cuti $cuti, string $tipe = 'baru')
    {
        $this->cuti = $cuti;
        $this->tipe = $tipe;
    }

    public function via($notifiable)
    {
        return [
            'database', 
            // FirebaseChannel::class, 
            WhatsAppChannel::class
        ];
    }

    public function toWhatsApp($notifiable)
    {
        $pemohon = $this->cuti->user->name ?? 'Karyawan';
        $tanggal = Carbon::parse($this->cuti->tanggal_mulai)->translatedFormat('d F Y');
        $link = route('cuti.show', $this->cuti->id);

        switch ($this->tipe) {
            case 'disetujui':
                $header = "✅ *CUTI DISETUJUI*";
                $pesan = "Halo {$notifiable->name}, pengajuan cuti Anda untuk tanggal *{$tanggal}* telah DISETUJUI.";
                break;
            case 'ditolak':
                $header = "❌ *CUTI DITOLAK*";
                $pesan = "Halo {$notifiable->name}, pengajuan cuti Anda untuk tanggal *{$tanggal}* DITOLAK.";
                break;
            case 'dibatalkan':
                $header = "⚠️ *CUTI DIBATALKAN*";
                $pesan = "Halo {$notifiable->name}, user *{$pemohon}* telah membatalkan pengajuan cutinya.";
                break;
            case 'baru':
            default:
                $header = "🆕 *PENGAJUAN CUTI BARU*";
                $pesan = "Halo {$notifiable->name}, ada pengajuan cuti baru dari *{$pemohon}* untuk tanggal *{$tanggal}*.\nMohon segera diperiksa.";
                break;
        }

        return [
            'message' => "{$header}\n\n{$pesan}\n\n🔗 *Link:* {$link}\n\n_Sistem Notifikasi_"
        ];
    }

    public function toArray($notifiable)
    {
        $pemohon = $this->cuti->user->name ?? 'Karyawan';
        $tanggal = Carbon::parse($this->cuti->tanggal_mulai)->translatedFormat('d F Y');

        switch ($this->tipe) {
            case 'disetujui':
                return [
                    'id' => $this->cuti->id,
                    'title' => 'Cuti Disetujui',
                    'message' => "Pengajuan cuti tanggal $tanggal disetujui.",
                    'icon' => 'fas fa-check-circle',
                    'color' => 'text-green-600',
                    'url' => route('cuti.show', $this->cuti->id)
                ];
            case 'ditolak':
                return [
                    'id' => $this->cuti->id,
                    'title' => 'Cuti Ditolak',
                    'message' => "Pengajuan cuti tanggal $tanggal ditolak.",
                    'icon' => 'fas fa-times-circle',
                    'color' => 'text-red-600',
                    'url' => route('cuti.show', $this->cuti->id)
                ];
            case 'dibatalkan':
                return [
                    'id' => $this->cuti->id,
                    'title' => 'Cuti Dibatalkan',
                    'message' => "$pemohon membatalkan cuti.",
                    'icon' => 'fas fa-ban',
                    'color' => 'text-gray-500',
                    'url' => route('cuti.show', $this->cuti->id)
                ];
            default:
                return [
                    'id' => $this->cuti->id,
                    'title' => 'Pengajuan Cuti Baru',
                    'message' => "$pemohon mengajukan cuti tanggal $tanggal.",
                    'icon' => 'fas fa-envelope',
                    'color' => 'text-blue-600',
                    'url' => route('cuti.show', $this->cuti->id)
                ];
        }
    }

    // === 3. FORMAT UNTUK FIREBASE (PUSH NOTIF HP) ===
    // public function toFirebase($notifiable)
    // {
    //     $data = $this->toArray($notifiable);
    //     return (new FirebaseMessage)
    //         ->withNotification([
    //             'title' => $data['title'],
    //             'body' => $data['message'],
    //         ])
    //         ->withData([
    //             'url' => $data['url']
    //         ])
    //         ->asMessage();
    // }
}