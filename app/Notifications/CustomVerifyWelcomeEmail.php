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

        $rol = $this->user->rol_id;

		$vista = match ($rol) {
			config('constantes.ROL_SOLICITANTE_PLANTA'),
			config('constantes.ROL_SOLICITANTE_PLANTA_XII')		=> 'emails.welcome-solicitante-planta',

			config('constantes.ROL_SOLICITANTE_PRODUCTOR')		=> 'emails.welcome-solicitante-productor',
			config('constantes.ROL_PERSONAL_GERENCIA')			=> 'emails.welcome-gerencia',

			config('constantes.ROL_PERSONAL_PRODUCCION'),
			config('constantes.ROL_PERSONAL_CALIDAD'),
			config('constantes.ROL_PERSONAL_MANTENCION'),
			config('constantes.ROL_PERSONAL_ROMANA')			=> 'emails.welcome-visualizacion',

			config('constantes.ROL_COORDINADOR'),
			config('constantes.ROL_COORDINADOR_XII')			=> 'emails.welcome-coordinador',

			default												=> 'emails.welcome-generico' // Aquí cae Administrador-IT
		};

        // $vista = match ($rol) {
        //     config('constantes.ROL_SOLICITANTE_PLANTA')      => 'emails.welcome-solicitante-planta',
        //     config('constantes.ROL_SOLICITANTE_PLANTA_XII')  => 'emails.welcome-solicitante-planta',

		// 	config('constantes.ROL_SOLICITANTE_PRODUCTOR')   => 'emails.welcome-solicitante-productor',

        //     config('constantes.ROL_PERSONAL_GERENCIA')       => 'emails.welcome-gerencia',

        //     config('constantes.ROL_PERSONAL_PRODUCCION')     => 'emails.welcome-visualizacion',
        //     config('constantes.ROL_PERSONAL_CALIDAD')        => 'emails.welcome-visualizacion',
        //     config('constantes.ROL_PERSONAL_MANTENCION')     => 'emails.welcome-visualizacion',
        //     config('constantes.ROL_PERSONAL_ROMANA')         => 'emails.welcome-visualizacion',

        //     config('constantes.ROL_COORDINADOR')             => 'emails.welcome-coordinador',
        //     config('constantes.ROL_COORDINADOR_XII')         => 'emails.welcome-coordinador',

        //     default                                          => 'emails.welcome-generico' // Aquí cae Administrador-IT
        // };

        return (new VerifyEmailMailable($this->user, $verificationUrl, $vista))->to($notifiable->email);
    }
}
