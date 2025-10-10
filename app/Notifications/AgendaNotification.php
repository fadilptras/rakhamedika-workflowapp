<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue; // <-- TAMBAHKAN INI
use Illuminate\Notifications\Notification;
use App\Models\Agenda;
use Illuminate\Support\Str;

class AgendaNotification extends Notification  
{
    use Queueable;

    public $agenda;
    public $tipe;
    public $pengundang;

    /**
     * Buat instance notifikasi baru.
     *
     * @param \App\Models\Agenda $agenda
     * @param string $tipe Konteks: 'undangan_baru', 'agenda_diperbarui', 'agenda_dibatalkan', 'pengingat'
     * @param string|null $pengundang Nama pembuat agenda
     * @return void
     */
    public function __construct(Agenda $agenda, string $tipe, ?string $pengundang = null)
    {
        $this->agenda = $agenda;
        $this->tipe = $tipe;
        $this->pengundang = $pengundang ?? $agenda->creator->name; // Fallback jika pengundang null
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $title = '';
        $message = '';
        $icon = 'fas fa-calendar-alt';
        $color = 'text-purple-600';
        $judulAgenda = Str::limit($this->agenda->title, 30);

        switch ($this->tipe) {
            case 'undangan_baru':
                $title = 'Undangan Agenda Baru';
                $message = "{$this->pengundang} mengundang Anda ke agenda '{$judulAgenda}'.";
                break;
            case 'agenda_diperbarui':
                $title = 'Agenda Diperbarui';
                $message = "Agenda '{$judulAgenda}' yang Anda ikuti telah diperbarui oleh {$this->pengundang}.";
                $icon = 'fas fa-calendar-check';
                $color = 'text-yellow-500';
                break;
            case 'agenda_dibatalkan':
                $title = 'Agenda Dibatalkan';
                $message = "Agenda '{$judulAgenda}' yang dibuat oleh {$this->pengundang} telah dibatalkan.";
                $icon = 'fas fa-calendar-times';
                $color = 'text-slate-500';
                break;
            
            // ===== LOGIKA NOTIFIKASI PENGINGAT (BARU) =====
            case 'pengingat':
                $title = 'Pengingat Agenda';
                $message = "Agenda '{$judulAgenda}' akan dimulai dalam 30 menit.";
                $icon = 'fas fa-bell';
                $color = 'text-blue-500';
                break;
            // ===============================================
        }

        return [
            'id' => $this->agenda->id,
            'title' => $title,
            'message' => $message,
            // ===== URL DIPERBARUI UNTUK SEMUA TIPE NOTIFIKASI =====
            'url' => route('dashboard', ['agenda_id' => $this->agenda->id]),
            // ========================================================
            'icon' => $icon,
            'color' => $color,
        ];
    }
}