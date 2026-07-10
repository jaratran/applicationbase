<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Log;

use App\Models\DesignParameter;
use App\Models\OperationalParameter;
use App\Models\User;

class NotificacionCorreoMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $destinatariosTo;
    public $destinatariosCc;

    public $subject;
    public $blade;
    public $urlDestino;
    public $data;

    public $designParameter;
    public $operationalParameter;

    /**
     * Crear nueva instancia de correo.
     *
     * @param \App\Models\User $user
     * @param string $blade Nombre del blade sin 'emails.' (ej: 'solicitud-creada')
     * @param array $data Datos que serán inyectados en el blade
     * @param object $designParameter
     * @param object $operationalParameter
     */
    public function __construct(string|array $to, string|array $cc, string $subject, string $blade, string $urlDestino, array $data)
    {
        $this->destinatariosTo      = $to;
        $this->destinatariosCc      = $cc;

        $this->subject              = $subject;
        $this->blade                = $blade;
        $this->urlDestino           = $urlDestino;
        $this->data                 = $data;

        $this->designParameter      = DesignParameter::first();
        $this->operationalParameter = OperationalParameter::first();
    }

    /**
     * Construir el mensaje.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this
                    ->to($this->destinatariosTo)
                    ->cc($this->destinatariosCc)

                    ->subject( $this->subject ?? __('emails.asunto_generico') )
                    ->view( $this->blade)

                    ->with( array_merge( $this->data, [
                                                        'urlDestino'           => $this->urlDestino,
                                                        'designParameter'      => $this->designParameter,
                                                        'operationalParameter' => $this->operationalParameter,
                                                    ]));

        if( $this->operationalParameter->audit_email_enabled === true ){    // Si el envío a correo de auditoría está habilitado

            if ( !empty($this->operationalParameter->audit_email) ) {       // Y se definió correo de auditoría
                $email->bcc($this->operationalParameter->audit_email);
            }

        }

        // ➕ Adjuntar PDF generado dinámicamente (si se requiere más adelante)
        // $pdf = PDF::loadView('pdf.solicitud', [...]);
        // $email->attachData($pdf->output(), 'solicitud.pdf', ['mime' => 'application/pdf']);

        return $email;
    }
}
