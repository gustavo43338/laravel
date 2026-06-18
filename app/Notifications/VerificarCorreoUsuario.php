<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerificarCorreoUsuario extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        return (new MailMessage)
            ->subject('Verifica tu correo — CondoAdmin')
            ->greeting('¡Hola, ' . $notifiable->nombre . '!')
            ->line('Tu cuenta fue creada por el administrador del condominio.')
            ->line('Haz clic en el botón para confirmar tu correo y activar tu acceso.')
            ->action('Verificar correo electrónico', $verificationUrl)
            ->line('Si no solicitaste esta cuenta, ignora este mensaje.')
            ->salutation('CondoAdmin — Sistema de Gestión');
    }
}
