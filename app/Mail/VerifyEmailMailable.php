<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use App\Models\OperationalParameter;

class VerifyEmailMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $verificationUrl;
    public $vista;

    public function __construct($user, $verificationUrl, $vista)
    {
        $this->user = $user;
        $this->verificationUrl = $verificationUrl;
        $this->vista = $vista;
    }

    public function build()
    {
        $email = $this->subject(__('auth.welcome_email_subject'))
                      ->view($this->vista)
                      ->with([
                          'user' => $this->user,
                          'verificationUrl' => $this->verificationUrl,
                      ]);

        // Parámetros operacionales desde base de datos
        $params = OperationalParameter::first();

        if ($params->audit_email_enabled === true && !empty($params->audit_email)) {
            $email->bcc($params->audit_email);
        }

        return $email;
    }
}
