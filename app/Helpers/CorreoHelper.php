<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

use App\Mail\NotificacionCorreoMailable;

use App\Models\OperationalParameter;
use App\Models\Solicitud;
use App\Models\User;

use App\Helpers\AgrupaDominios;

class CorreoHelper
{
    /**
     * Envía un correo al solicitante notificando que la solicitud fue creada.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Solicitud $solicitud
     * @return void
     */
    public static function enviarCorreoSolicitudCreada($solicitud)
    {
        try {
            $destinatario = [Auth::user()->email]; // Usuario que efectúa la solicitud, pero en formato array (con 1 sólo elemento).

            $solicitudes = Solicitud::with([                                                                    // Obtenemos la líneas de los retiros creados para ponerlos en el correo
                                                'maquila.empresa:id,razon_social',
                                                'maquila.sucursal:id,nombre_sucursal',
                                                'usuario:id,nombre_usuario,apellidos_usuario',
                                                'retiros' => function ($query) {
                                                                                    $query->orderByDesc('id'); // ordena retiros dentro de cada solicitud
                                                                                },
                                                'retiros.tipoRetiro:id,nombre'
                                        ])
                                        ->where('id', $solicitud->id)                                           // Sólo 1 solicitud + sus retiros
                                        ->get();

            $subject = __('emails.solicitud_creada_subject', [
                                                                'retiroIds'     => $solicitudes->first()->retiros->pluck('id')->implode(','),
                                                                'fechaRetiros'  => $solicitudes->first()->created_at->format('d-m-Y'),
                                                            ]);

            $pivote = [
                'campo' => 'solicitud_id',
                'valor' => $solicitud->id,
            ];

            $urlPivoteCorreo = route('solicitudes-retiro.ver-desde-token', [
                                                                            'token' => Crypt::encrypt($pivote),
                                                                        ]);

            Mail::send(new NotificacionCorreoMailable(
                                                        $destinatario,                                          // Destinatario (array de largo 1)
                                                        [],                                                     // Sin con copia

                                                        $subject,                                               // 📌 Subject personalizado
                                                        'emails.solicitud-creada',                              // Blade con el email
                                                        $urlPivoteCorreo,                                       // URL para el botón

                                                        [                                                       // Arreglo con contenido para el correo
                                                            'usuario' => Auth::user(),                          // Usuario para apelativo
                                                            'solicitudes' => $solicitudes                       // Solicitudes para tabla de retiros
                                                        ]
                                                    ));
        }
        catch (\Exception $e) {
            Log::error("Error al enviar correo solicitud creada : " . $e->getMessage());
        }
    }

    /**
     * Envía un correo al coordinador notificando que se ha creado una solicitud para el día de hoy.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Solicitud $solicitud
     * @return void
     */
    public static function enviarCorreoSolicitudCreadaParaHoy($solicitud, $fechaHoy)
    {
        try {
            $operationalParameter = OperationalParameter::first();

			// $coordinadores = User::where('rol_id', config('constantes.ROL_COORDINADOR'))        // Listado de coordinadores X región a quienes notificar
			// 						->pluck('email')
			// 						->toArray();

			// Listado de coordinadores cuyas regiones operativas (pueden tener más de una) incluyen la región de la solicitud
			$regionOperativa = $solicitud->region_operativa_id;
			$coordinadores = User::whereIn('rol_id', [ config('constantes.ROL_COORDINADOR'), config('constantes.ROL_COORDINADOR_XII') ])
									->get()
									->filter(function ($user) use ($regionOperativa) {	// Aquí consideramos que algunos tienen más de una región operativa
																						return in_array($regionOperativa, $user->regiones_operativas_ids);
																					})
									->pluck('email')
									->toArray();

            if( $operationalParameter->notify_admins_as_coordinators === true ){                    // Si está habilitada en Parametros Operacionales la notificación a Admins-IT
                $admins_it = User::where('rol_id', config('constantes.ROL_ADMINISTRADOR_IT'))       // en admins_it va el listado de correos
                                    ->pluck('email')
                                    ->toArray();
            }
            else{
                $admins_it = [];                                                                    // y si NO admins_it queda vacío
            }

            $solicitudes = Solicitud::with([                                                                    // Obtenemos la líneas de los retiros creados para ponerlos en el correo
                                                'maquila.empresa:id,razon_social',
                                                'maquila.sucursal:id,nombre_sucursal',
                                                'usuario:id,nombre_usuario,apellidos_usuario',
                                                'retiros' => function ($query) {
                                                                                    $query->orderByDesc('id'); // ordena retiros dentro de cada solicitud
                                                                                },
                                                'retiros.tipoRetiro:id,nombre'
                                        ])
                                        ->where('id', $solicitud->id)                                           // Sólo 1 solicitud + sus retiros
                                        ->get();

            $pivote = [
                'campo'      => 'solicitud_id',
                'valor'      => $solicitud->id,
                'fechaHoy'   => $fechaHoy,              // Aquí incluímos la fecha de HOY usada en la comparación a fin de que después el INDEX la use para filtrar lo retiros.
            ];

            $urlPivoteCorreo = route('solicitudes-retiro.ver-desde-token', [
                                                                            'token' => Crypt::encrypt($pivote),
                                                                        ]);

            Mail::send(new NotificacionCorreoMailable(
                                                        $coordinadores,                                         // Destinatario(s)
                                                        $admins_it,                                             // Con copia (vacio o listado de correo de administradores IT)

                                                        __('emails.solicitud_creada_parahoy_subject'),          // Subject
                                                        'emails.solicitud-creada-parahoy',                      // Blade con el email
                                                        $urlPivoteCorreo,                                       // URL para el botón

                                                        [                                                       // Arreglo con contenido para el correo
                                                            'solicitudes' => $solicitudes,                      // Solicitudes para tabla de retiros
                                                            'fechaHoy'    => $fechaHoy                          // Fecha de hoy para que pivotee en el correo
                                                        ]
                                                    ));

        }
        catch (\Exception $e) {
            Log::error("Error al enviar correo solicitud creada para hoy : " . $e->getMessage());
        }
    }

    /**
     * Envía un correo al solicitante notificando que el retiro fue comentado por el coordinador.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Solicitud $solicitud
     * @return void
     */
    public static function enviarCorreoRetiroComentadoPorCoordinador($retiro)
    {
        try {
            $solicitud = Solicitud::findOrFail($retiro->solicitud_id); // Accedemos a la solicitud que contiene el retiro

            $subject = __('emails.retiro_comentado_coordinador_subject', [
                                                                            'retiroId'     => $retiro->id,
                                                                            'fechaRetiro'  => $solicitud->created_at->format('d-m-Y'),
                                                                        ]);

            $usuario = User::findOrFail($solicitud->usuario_id); // Usuario que creo la solicitud para los apelativos en el correo
            $destinatario = [ $usuario->email ]; // Correo de usuario (pero en formato array con 1 sólo elemento) para enviarle la notificación

            $pivote = [
                'campo' => 'retiro_id',
                'valor' => $retiro->id,
            ];

            $urlPivoteCorreo = route('solicitudes-retiro.ver-desde-token', [
                                                                            'token' => Crypt::encrypt($pivote),
                                                                        ]);

            Mail::send(new NotificacionCorreoMailable(
                                                        $destinatario,                                          // Destinatario (array de largo 1)
                                                        [],                                                     // Sin con copia

                                                        $subject,                                               // 📌 Subject personalizado
                                                        'emails.retiro-comentado-coordinador',                  // Blade con el email
                                                        $urlPivoteCorreo,                                       // URL para el botón

                                                        [                                                       // Arreglo con contenido para el correo
                                                            'usuario' => $usuario,                              // Objeto Usuario para apelativo en el correo
                                                            'retiroId' => $retiro->id,                          // ID de Retiro para incrustarlo en el body del correo
                                                        ]
                                                    ));
        }
        catch (\Exception $e) {
            Log::error("Error al enviar correo de retiro comentado por el coordinador : " . $e->getMessage());
        }
    }

    /**
     * Envía un correo al coordinador notificando que el retiro fue comentado por el solicitante.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Solicitud $solicitud
     * @return void
     */
    public static function enviarCorreoRetiroComentadoPorSolicitante($retiro)
    {
        try {
            $operationalParameter = OperationalParameter::first();

            // $coordinadores = User::where('rol_id', config('constantes.ROL_COORDINADOR'))    // Listado de coordinadores a quienes notificar
            //                         ->pluck('email')
            //                         ->toArray();

			// Listado de coordinadores cuyas regiones operativas (pueden tener más de una) incluyen la región de la solicitud.
			$regionOperativa = $retiro->region_operativa_id;
			$coordinadores = User::whereIn('rol_id', [ config('constantes.ROL_COORDINADOR'), config('constantes.ROL_COORDINADOR_XII') ])
									->get()
									->filter(function ($user) use ($regionOperativa) {	// Aquí consideramos que algunos tienen más de una región operativa
																						return in_array($regionOperativa, $user->regiones_operativas_ids);
																					})
									->pluck('email')
									->toArray();

            if( $operationalParameter->notify_admins_as_coordinators === true ){                    // Si está habilitada en Parametros Operacionales la notificación a Admins-IT
                $admins_it = User::where('rol_id', config('constantes.ROL_ADMINISTRADOR_IT'))       // en admins_it va el listado de correos
                                    ->pluck('email')
                                    ->toArray();
            }
            else{
                $admins_it = [];                                                                    // y si NO admins_it queda vacío
            }

            // Accedemos a la solicitud que contiene el retiro
            $solicitud = Solicitud::with([                                                                    // Obtenemos la líneas de los retiros creados para ponerlos en el correo
                                                'maquila.empresa:id,razon_social',
                                                'maquila.sucursal:id,nombre_sucursal',
                                                'usuario:id,nombre_usuario,apellidos_usuario',
                                                'retiros' => function ($query) {
                                                                                    $query->orderByDesc('id'); // ordena retiros dentro de cada solicitud
                                                                                },
                                                'retiros.tipoRetiro:id,nombre'
                                        ])
                                        ->findOrFail($retiro->solicitud_id);                                    // Sólo 1 solicitud + sus retiros

            $subject = __('emails.retiro_comentado_solicitante_subject', [
                                                                            'retiroId'          => $retiro->id,
                                                                            'fechaRetiro'       => $solicitud->created_at->format('d-m-Y'),
                                                                            'proveedorNombre'   => $solicitud->maquila->empresa->razon_social,
                                                                        ]);

            $pivote = [
                'campo' => 'retiro_id',
                'valor' => $retiro->id,
            ];

            $urlPivoteCorreo = route('solicitudes-retiro.ver-desde-token', [
                                                                            'token' => Crypt::encrypt($pivote),
                                                                        ]);

            Mail::send(new NotificacionCorreoMailable(
                                                        $coordinadores,                                                     // Destinatario(s)
                                                        $admins_it,                                                         // Con copia (vacio o listado de correo de administradores IT)

                                                        $subject,                                                           // 📌 Subject personalizado
                                                        'emails.retiro-comentado-solicitante',                              // Blade con el email
                                                        $urlPivoteCorreo,                                                   // URL para el botón

                                                        [                                                                   // Arreglo con contenido para el correo
                                                            'retiroId'          => $retiro->id,                             // ID de Retiro para incrustarlo en el body del correo
                                                            'fechaRetiro'       => $solicitud->created_at->format('d-m-Y'), // Fecha de cuando fue solicitado el Retiro
                                                        ]
                                                    ));
        }
        catch (\Exception $e) {
            Log::error("Error al enviar correo de retiro comentado por el solicitante : " . $e->getMessage());
        }
    }

    /**
     * Envía un correo al solicitante notificando que el retiro fue aprobado por el coordinador.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Solicitud $solicitud
     * @return void
     */
    public static function enviarCorreoRetiroAprobado($retiro)
    {
        try {
            $solicitud = Solicitud::findOrFail($retiro->solicitud_id); // Accedemos a la solicitud que contiene el retiro
            $usuario = User::findOrFail($solicitud->usuario_id); // Usuario que creo la solicitud para los apelativos en el correo
            $destinatario = [ $usuario->email ]; // Correo de usuario (pero en formato array con 1 sólo elemento) para enviarle la notificación

            $pivote = [
                'campo' => 'retiro_id',
                'valor' => $retiro->id,
            ];

            $subject = __('emails.retiro_aprobado_subject', [
                                                                'retiroId'     => $retiro->id,
                                                                'fechaRetiro'  => $solicitud->created_at->format('d-m-Y'),
                                                            ]);

            $urlPivoteCorreo = route('solicitudes-retiro.ver-desde-token', [
                                                                            'token' => Crypt::encrypt($pivote),
                                                                        ]);

            Mail::send(new NotificacionCorreoMailable(
                                                        $destinatario,                                          // Correo destinatario (array de largo 1)
                                                        [],                                                     // Sin con copia

                                                        $subject,                                               // 📌 Subject personalizado
                                                        'emails.retiro-aprobado',                               // Blade con el email
                                                        $urlPivoteCorreo,                                       // URL para el botón

                                                        [                                                       // Arreglo con contenido para el correo
                                                            'usuario'   => $usuario,                            // Objeto Usuario para apelativo en el correo
                                                            'retiroId'  => $retiro->id,                         // ID de retiro para incrustarlo en el body del correo
                                                        ]
                                                    ));
        }
        catch (\Exception $e) {
            Log::error("Error al enviar correo de retiro aprobado por el coordinador : " . $e->getMessage());
        }
    }

}
