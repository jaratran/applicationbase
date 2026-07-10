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

    /**
     * Envía un correo al solicitante notificando que el reitro fue aprobado por el coordinador.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Solicitud $solicitud
     * @return void
     */
    public static function enviarCorreoPlanificacionCreada($planificacion)
    {
        try {
            $retiro = $planificacion->retiro;                           // Accedemos al retiro asociado a la planificación
            $usuario = $planificacion->retiro->solicitud->usuario;      // Usuario que creo la solicitud para los apelativos en el correo

            $destinatario = [ $usuario->email ]; // Correo de usuario (pero en formato array con 1 sólo elemento) para enviarle la notificación

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

            $subject = __('emails.retiro_planificado_subject', [
                                                                'retiroId'          => $retiro->id,
                                                                'fechaRetiro'       => $solicitud->created_at->format('d-m-Y'),
                                                                'proveedorNombre'   => $solicitud->maquila->empresa->razon_social,
                                                            ]);

            $pivote = [
                'campo' => 'retiro_id',
                'valor' => $planificacion->retiro_id,
            ];

            $urlPivoteCorreo = route('planificaciones-retiro.ver-desde-token', [
                                                                                'token' => Crypt::encrypt($pivote),
                                                                            ]);

            Mail::send(new NotificacionCorreoMailable(
                                                        $destinatario,                                          // Correo destinatario (array de largo 1)
                                                        [],                                                     // Sin con copia

                                                        $subject,                                               // 📌 Subject personalizado
                                                        'emails.planificacion-creada',                          // Blade con el email
                                                        $urlPivoteCorreo,                                       // URL para el botón

                                                        [                                                       // Arreglo con contenido para el correo
                                                            'usuario' => $usuario,                              // Objeto Usuario para apelativo en el correo
                                                            'planificacion' => $planificacion,                  // Objeto Planificación con los datos para el correo
                                                        ]
                                                    ));
        }
        catch (\Exception $e) {
            Log::error("Error al enviar correo planificación creada : " . $e->getMessage());
        }
    }

    public static function enviarCorreoProgramaTransportista($empresa, $programaDiario, $datos)
    {
        try {
            $destinatario = [ $empresa->email_contacto ]; // ✔️ Array con un solo correo destinatario

            $subject = __('emails.programa_diario_transportista_subject', [
                                                                                'fechaPrograma'   => \Carbon\Carbon::parse($programaDiario->fecha_programa)->format('d-m-Y'),
                                                                                'transportistaNombre' => $empresa->razon_social,
                                                                            ]);
            $operationalParameter = OperationalParameter::first();

            Log::info('[Programa Diario] Preparando envío de notificación a transportistas', [
                'programa_diario_id' => $programaDiario->id,
                'version'            => $programaDiario->version,
                'to'                 => $destinatario,
                'cc'                 => [],
                'bcc'                => !empty(optional($operationalParameter)->audit_email)
                                            ? [optional($operationalParameter)->audit_email]
                                            : []
            ]);

            Mail::send(new NotificacionCorreoMailable(
                $destinatario,                                 // 📬 Correo destinatario
                [],                                             // 🕶 Sin CC

                $subject,                                       // 📌 Subject personalizado
                'emails.programa-diario-transportista',         // 📄 Blade del correo
                '',                                             // 🚫 Sin URL destino para botón en correo

                [                                               // 📦 Contenido del correo
                    'empresa'         => $empresa,
                    'programaDiario'  => $programaDiario,
                    'datos'           => $datos,
                ]
            ));
        } catch (\Exception $e) {
            Log::error("Error al enviar correo a empresa transportista ID {$empresa->id}: " . $e->getMessage());
        }
    }

    /**
     * Envía un correo a usuarios con roles internos notificando la creación de una planificación para Región XII.
     *
     * @param  array                           $destinatarios     Arreglo con correos electrónicos
     * @param  \App\Models\ProgramaDiario      $programaDiario    Cabecera del programa diario
     * @param  \Illuminate\Support\Collection  $detalles          Detalle emitido de la última versión
     * @return void
     */
	public static function enviarCorreoTransportistaPlanificacionRegionXII($planificacion)
	{
		try {
			$planificacion->loadMissing([
				'camion.empresa',
				'camion.tipoCamion',
				'conductor',
				'rampla'
			]);

			// Empresa transportista dueña del camión (no la productora de materia prima vinculada con la sucursal mediante tabla maquila)
			$empresa = $planificacion->camion?->empresa;

			if (!$empresa) {
				Log::warning('[Planificación Región XII] No se encontró empresa transportista asociada', [
					'planificacion_id' => $planificacion->id
				]);
				return;
			}

			if (empty($empresa->email_contacto)) {
				Log::warning('[Planificación Región XII] Empresa transportista sin email de contacto', [
					'empresa_id' => $empresa->id ?? null,
					'planificacion_id' => $planificacion->id
				]);
				return;
			}

			// Correo de la empresa transportista
			$destinatario = [$empresa->email_contacto]; // ✔️ Array con un solo correo destinatario
			$eta = optional($planificacion->eta_calculada)
					? \Carbon\Carbon::parse($planificacion->eta_calculada)
					: null;

			$subject = __('emails.planificacion_region_xii_subject', [	'fechaRetiro' => $eta?->format('d-m-Y'),
																		'horaRetiro'  => $eta?->format('H:i'),
																	]);

			$operationalParameter = OperationalParameter::first();

			Log::info('[Planificación Región XII] Preparando envío de notificación a empresa transportista', [
				'planificacion_id'   => $planificacion->id,

                'to'                 => $destinatario,
                'cc'                 => [],
                'bcc'                => !empty(optional($operationalParameter)->audit_email)
                                            ? [optional($operationalParameter)->audit_email]
                                            : []
			]);

            Mail::send(new NotificacionCorreoMailable(
                $destinatario,                                                  // 📬 Correo destinatario
                [],                                                             // 🕶 Sin CC

                $subject,                                                       // 📌 Subject personalizado
                'emails.planificacion-creada-region-xii-transportista',         // 📄 Blade del correo
                '',                                                             // 🚫 Sin URL destino para botón en correo

                [                                                               // 📦 Contenido del correo
                    'empresa'       => $empresa,
					'planificacion' => $planificacion,                          // Objeto Planificación con los datos para el correo
                ]
            ));

		} catch (\Exception $e) {

			Log::error(
				"Error al enviar correo a empresa transportistas con planificación Región XII ID {$planificacion->id}: "
				. $e->getMessage()
			);

		}
	}

    /**
     * Envía un correo a usuarios internos notificando la emisión del Programa Diario.
     *
     * @param  array                           $destinatarios     Arreglo con correos electrónicos
     * @param  \App\Models\ProgramaDiario      $programaDiario    Cabecera del programa diario
     * @param  \Illuminate\Support\Collection  $detalles          Detalle emitido de la última versión
     * @return void
     */
    public static function enviarCorreoProgramaDiarioEmitido(array $destinatarios, $programaDiario, $detalles)
    {
        try {
            $subject = __('emails.programa_diario_interno_subject', [
                                                                        'fechaPrograma'   => $programaDiario->fecha_programa->format('d-m-Y'),
                                                                        'versionPrograma' => $programaDiario->version,
                                                                    ]);

            $pivote = [
                'campo' => 'programa_diario_id',
                'valor' => $programaDiario->id,
            ];

            $urlPivoteCorreo = route('programa-diario.ver-desde-token', [
                                                                            'token' => Crypt::encrypt($pivote),
                                                                        ]);

            // 🧮 Cálculo del total de kilogramos estimados (con exclusión de cancelados)
            $totalKilosEstimados = $detalles
                                        ->filter(fn($detalle) => $detalle['estado'] !== config('constantes.ESTADO_RETIRO_CANCELADO'))
                                        ->sum('kg_estimados');


            // Agrupar todos los destinatarios por dominio.
            // Para evitar la excepción bloqueante de GMAIL 'Error 451 Multiple destination domains' ocasionada cuando los correos pertenenen a más de un dominio.
            $toArray = $destinatarios['destinatariosRolesInternos'] ?? [];
            $ccArray = $destinatarios['destinatariosAdminIT']      ?? [];

            $grupos  = AgrupaDominios::agrupaDominios($toArray, $ccArray);

            $operationalParameter = OperationalParameter::first();

            foreach ($grupos as $dominio => $destinos) {                // Recorrer cada grupo (un dominio a la vez)

                // INSERCCION PARA EVITAR ERROR CUANDO NO VIENE NADA EN TO o CC
                $to = $destinos['to'] ?? [];
                $cc = $destinos['cc'] ?? [];

                // Si un grupo viene sin destinatarios, se omite de forma segura
                if (empty($to) && empty($cc)) {
                    Log::warning('[Programa Diario] Grupo sin destinatarios, se omite envío', [
                        'programa_diario_id' => $programaDiario->id,
                        'version'            => $programaDiario->version,
                        'dominio'            => $dominio,
                    ]);
                    continue;
                }

                Log::info('[Programa Diario] Preparando envío de notificación a usuarios internos', [
                    'programa_diario_id' => $programaDiario->id,
                    'version'            => $programaDiario->version,
                    'dominio'            => $dominio,
                    'to'                 => $to,
                    'cc'                 => $cc,
                    'bcc'                => !empty(optional($operationalParameter)->audit_email)
                                                ? [optional($operationalParameter)->audit_email]
                                                : []
                ]);

                Mail::send(new NotificacionCorreoMailable(
                    $to,                                                // 📬 To: destinatariosRolesInternos
                    $cc,                                                // 🕶  CC: destinatariosAdminIT

                    $subject,                                           // 📌 Subject personalizado
                    'emails.programa-diario-interno',                   // 📄 Blade del correo
                    $urlPivoteCorreo,                                   // 🔗 URL con botón para ver el programa

                    [                                                   // 📦 Datos disponibles en el blade
                        'programaDiario'      => $programaDiario,
                        'detalles'            => $detalles,
                        'totalKilosEstimados' => $totalKilosEstimados
                    ]
                ));
            }

        } catch (\Exception $e) {
            Log::error("Error al enviar correo de programa diario ID {$programaDiario->id}: " . $e->getMessage());
        }
    }

    /**
     * Envía un correo a usuarios con roles internos notificando la creación de una planificación para Región XII.
     *
     * @param  array                           $destinatarios     Arreglo con correos electrónicos
     * @param  \App\Models\ProgramaDiario      $programaDiario    Cabecera del programa diario
     * @param  \Illuminate\Support\Collection  $detalles          Detalle emitido de la última versión
     * @return void
     */
	public static function enviarCorreoRolesInternosPlanificacionRegionXII($planificacion)
	{
		try {
			$subject = __('emails.planificacion_region_xii_subject', [
				'fechaRetiro' => optional($planificacion->eta_calculada)
									? \Carbon\Carbon::parse($planificacion->eta_calculada)->format('d-m-Y')
									: null,

				'horaRetiro'  => optional($planificacion->eta_calculada)
									? \Carbon\Carbon::parse($planificacion->eta_calculada)->format('H:i')
									: null,
			]);

			$pivote = [
				'campo' => 'planificacion_id',
				'valor' => $planificacion->id,
			];

			$urlPivoteCorreo = route('planificaciones-retiro.ver-desde-token', [
																					'token' => Crypt::encrypt($pivote),
																				]);

			$RolesInternos = [
				config('constantes.ROL_PERSONAL_GERENCIA'),
				config('constantes.ROL_PERSONAL_PRODUCCION'),
				config('constantes.ROL_PERSONAL_CALIDAD'),
				config('constantes.ROL_PERSONAL_MANTENCION'),
				config('constantes.ROL_PERSONAL_ROMANA'),
			];

			$destinatariosRolesInternos = User::whereIn('rol_id', $RolesInternos)
												->pluck('email')
												->filter()
												->unique()
												->toArray();

			$destinatariosAdminIT = User::where('rol_id', config('constantes.ROL_ADMINISTRADOR_IT'))
											->pluck('email')
											->filter()
											->unique()
											->toArray();

			if (empty($destinatariosRolesInternos) && empty($destinatariosAdminIT)) {
				Log::warning("No se encontraron destinatarios internos para notificar programa diario ID {$programaDiario->id}");
				return;
			}

			$toArray = $destinatariosRolesInternos ?? [];
			$ccArray = $destinatariosAdminIT ?? [];
			$grupos  = AgrupaDominios::agrupaDominios($toArray, $ccArray);

			$operationalParameter = OperationalParameter::first();

			foreach ($grupos as $dominio => $destinos) {

				$to = $destinos['to'] ?? [];
				$cc = $destinos['cc'] ?? [];

				if (empty($to) && empty($cc)) {

					Log::warning('[Planificación Región XII] Grupo sin destinatarios, se omite envío', [
						'planificacion_id' => $planificacion->id,
						'dominio' => $dominio,
					]);

					continue;
				}

				Log::info('[Planificación Región XII] Preparando envío de notificación a usuarios internos', [
					'planificacion_id' => $planificacion->id,
					'dominio' => $dominio,
					'to' => $to,
					'cc' => $cc,
					'bcc' => !empty(optional($operationalParameter)->audit_email)
								? [optional($operationalParameter)->audit_email]
								: []
				]);

				Mail::send(new NotificacionCorreoMailable(
					$to,
					$cc,

					$subject,
					'emails.planificacion-creada-region-xii-rolesinternos',

					$urlPivoteCorreo,

					[
						'planificacion' => $planificacion,                  // Objeto Planificación con los datos para el correo
					]
				));
			}

		} catch (\Exception $e) {

			Log::error(
				"Error al enviar correo planificación Región XII ID {$planificacion->id}: "
				. $e->getMessage()
			);

		}
	}

    /**
     * Envía un correo al Transportista señalando el que retiro se cancelo antes de ser efectuadoa (cierre) .
     *
     * @param  array                           $destinatarios     Arreglo con correos electrónicos
     * @param  \App\Models\ProgramaDiario      $programaDiario    Cabecera del programa diario
     * @param  \Illuminate\Support\Collection  $detalles          Detalle emitido de la última versión
     * @return void
     */
    public static function enviarCorreoPlanificacionCancelada($planificacion)
    {
        try {
            $empresa = optional($planificacion->camion)->empresa;       // Correo de contacto de Trasportista (pero en formato array con 1 sólo elemento) para enviarle la notificación
            if (empty($empresa?->email_contacto)) {
                Log::info('[Correo] Empresa sin email de contacto, no se envía notificación', [
                    'empresa_id'       => optional($empresa)->id,
                    'planificacion_id' => $planificacion->id,
                ]);
                return;
            }

            $destinatario = [ $empresa->email_contacto ];               // ✔️ Array con un solo correo destinatario

            $datos = [
                        'numero_retiro'        => $planificacion->retiro_id,
                        'fecha_hora_agendada'  => optional($planificacion->fecha_hora_planificada)?->format('d-m-Y H:i'),
                        'patente_camion'       => optional($planificacion->camion)?->patente ?? '—',
                        'patente_rampla'       => $planificacion->patente_rampla ?? '—',
                        'tipo_camion'          => optional($planificacion->camion?->tipoCamion)?->nombre ?? '—',
                        'nombre_chofer'        => optional($planificacion->conductor)?->nombre_completo ?? '—',
                        'rut_chofer'           => optional($planificacion->conductor)?->rut ?? '—',
                    ];

            Mail::send(new NotificacionCorreoMailable(
                                                        $destinatario,                                              // Correo destinatario (array de largo 1)
                                                        [],                                                         // Sin con copia

                                                        __('emails.planificacion_cancelada_subject'),               // Subject
                                                        'emails.planificacion-cancelada',                           // Blade con el email
                                                        '',                                                         // 🚫 Sin URL destino para botón en correo

                                                        [
                                                            'planificacion' => $planificacion,                  // Objeto Planificación con los datos para el correo
                                                            'empresa'       => $empresa,
                                                            'datos'         => $datos,                          // 🔥 ahora la variable $datos existirá en el blade
                                                        ]
                                                    ));
        }
        catch (\Exception $e) {
            Log::error('Error al enviar correo para notificar al transportista de la cancelación de la planificación programada: ', [
                'empresa_id'       => $empresa->id,
                'planificacion_id' => $planificacion->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
