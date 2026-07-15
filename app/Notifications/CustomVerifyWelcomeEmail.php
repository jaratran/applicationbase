<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

use App\Mail\VerifyEmailMailable;
use Illuminate\Support\Facades\Mail;

use App\Models\OperationalParameter;
use Illuminate\Support\Facades\Log;


class CustomVerifyWelcomeEmail extends Notification
{
    protected $user;

    /**
     * Crear una nueva instancia de la notificación.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Determina los canales por los que se enviará la notificación.
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Genera el enlace firmado de verificación estándar de Laravel.
     */
    protected function verificationUrl($notifiable)
    {
        $operationalParameter = OperationalParameter::first();
        $expirationTime = $operationalParameter->verification_expiration_time ?? 60; // Tiempo de expiración en minutos desde la base de datos o 60 por defecto

        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes( $expirationTime ), // <--- Tiempo de expiración configurable desde la base de datos. Omite mirar el config de auth.verification.expire
            [
                'id'   => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    /**
     * Construye el mensaje que será enviado por correo.
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new VerifyEmailMailable($this->user, $verificationUrl, 'emails.welcome-generico'))
            ->to($notifiable->email);
    }
}
