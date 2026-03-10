<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeadlineReminder extends Notification
{
    use Queueable;

    public $totaleNotifiche;

    // Passiamo il numero di scadenze alla notifica
    public function __construct($totaleNotifiche)
    {
        $this->totaleNotifiche = $totaleNotifiche;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🔔 Promemoria Scadenze Imminenti - UniLab')
            ->greeting('Ciao ' . $notifiable->name . '!')
            ->line('Questo è un promemoria automatico: hai ' . $this->totaleNotifiche . ' scadenze nei prossimi 7 giorni (o già scadute).')
            ->line('Accedi al gestionale per controllare i dettagli di Task, Pubblicazioni e Milestone per i tuoi progetti.')
            ->action('Vai alla Dashboard', route('dashboard'))
            ->salutation('Buon lavoro dal team UniLab!');
    }
}
