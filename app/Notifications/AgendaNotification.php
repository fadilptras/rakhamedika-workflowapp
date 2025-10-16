<?php

namespace App\Notifications;

use App\Models\Agenda;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AgendaNotification extends Notification
{
    use Queueable;

    protected $agenda;
    protected $type;
    protected $creatorName;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Agenda $agenda, $type, $creatorName)
    {
        $this->agenda = $agenda;
        $this->type = $type;
        $this->creatorName = $creatorName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $title = '';
        $message = '';
        $icon = 'fas fa-calendar-alt';
        $color = 'text-blue-500';

        switch ($this->type) {
            case 'undangan_baru':
                $title = 'Undangan Agenda Baru';
                $message = $this->creatorName . ' mengundang Anda ke agenda "' . $this->agenda->title . '".';
                break;
            case 'agenda_diperbarui':
                $title = 'Agenda Diperbarui';
                $message = $this->creatorName . ' memperbarui detail agenda "' . $this->agenda->title . '".';
                $icon = 'fas fa-calendar-check';
                $color = 'text-yellow-500';
                break;
            case 'agenda_dibatalkan':
                $title = 'Agenda Dibatalkan';
                $message = $this->creatorName . ' telah membatalkan agenda "' . $this->agenda->title . '".';
                $icon = 'fas fa-calendar-times';
                $color = 'text-red-500';
                break;
        }

        return [
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'color' => $color,
            // --- PERUBAHAN DI SINI ---
            // Menggunakan route helper untuk membuat URL dengan parameter
            'url' => route('dashboard', ['agenda_id' => $this->agenda->id]),
        ];
    }
}