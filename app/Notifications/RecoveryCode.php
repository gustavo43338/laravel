<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class RecoveryCode extends Notification
{
    use Queueable;

    public function __construct(public string $code, public ?Carbon $expires = null)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $line = 'Tu código de recuperación de contraseña es: ' . $this->code;
        if ($this->expires) {
            $line .= ' (expira a las ' . $this->expires->toDateTimeString() . ')';
        }

        return (new MailMessage)
            ->subject('Código de recuperación — CondoAdmin')
            ->greeting('Hola ' . ($notifiable->nombre ?? ''))
            ->line($line)
            ->line('Si no solicitaste este código, ignora este mensaje.');
    }
}
