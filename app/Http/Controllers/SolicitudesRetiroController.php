<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

use Illuminate\Validation\ValidationException;

use App\Mail\SolicitudCreadaMailable;
use App\Helpers\CorreoHelper;
use Carbon\Carbon;

use App\Models\Solicitud;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Sucursal;
use App\Models\Maquila;
use App\Models\Retiro;
use App\Models\RetiroComentario;

class SolicitudesRetiroController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // 🧹 Si viene el parámetro especial (desde el botón), limpiamos el pivote
        if ($request->has('chaoPivote')) {
            session()->forget('pivoteCorreo');

            // 🔁 Redirección a sí mismo para limpiar la URL
            return redirect()->route('solicitudes-retiro.index');
        }

        // 🔍 Si petición tuvo origen en correo de notificación de creación de solicitud y no eliminamos el pivote de contexto
        $pivoteCorreo = session('pivoteCorreo'); // nombre claro, genérico y expresivo
        if ($pivoteCorreo) {
            $campo = $pivoteCorreo['campo'] ?? 'ninguno';
            $valor = $pivoteCorreo['valor'] ?? null;
            $fechaHoy  = $pivoteCorreo['fechaHoy']  ?? null; // En caso de venir de notificación de creacíon para HOY
        }
        else{
            $campo = 'ninguno';
            $valor = null;
            $fechaHoy  = null;
        }

        // Establecemos rol del usuario de la sesión y si es solicitante planta o solicitante productor.
        $rolSesion              = Auth::user()->rol_id;
        $esSolicitantePlanta    = $rolSesion === config('constantes.ROL_SOLICITANTE_PLANTA');
        $esSolicitanteProductor = $rolSesion === config('constantes.ROL_SOLICITANTE_PRODUCTOR');
		$regionesOperativas     = Auth::user()->regiones_operativas_ids;

        switch ($campo) {
            case 'solicitud_id':
                // Enviamos sólo los retiros de aquella solicitud
                $solicitudes = Solicitud::visiblesSegunRol($esSolicitantePlanta, $esSolicitanteProductor)       // 🔒 Aplica scope de modelo Solicitud - filtro de visibilidad según el rol del usuario (planta, productor, o acceso total)
										->porRegionesOperativas($regionesOperativas)
                                        ->with([
                                                    'maquila.empresa:id,razon_social',
                                                    'maquila.sucursal:id,nombre_sucursal,comuna_id',
													'maquila.sucursal.comuna:id,region_id',
                                                    'usuario:id,nombre_usuario,apellidos_usuario',
                                                    'retiros' => function ($query) use ($fechaHoy) {
                                                                                                        if ($fechaHoy) {
                                                                                                            $query->whereDate('fecha_retiro', $fechaHoy);
                                                                                                        }
                                                                                                        $query->orderByDesc('id'); // Siempre se ordenan los retiros dentro de la solicitud
                                                                                                    },
                                                    'retiros.tipoRetiro:id,nombre'
                                                ])
                                        ->where('id', $valor)       // Filtra por la solicitud específica
                                        ->get();                    // Sólo 1 solicitud + sus retiros

                return view('solicitudes-retiro.index', compact('solicitudes', 'pivoteCorreo')); // Inyectamos pivote para que en el Blade muestre boton liberar pivote
                break;

            case 'retiro_id':
                // Enviamos sólo el retiros cuya novedad fué notificada por correo
                $retiro = Retiro::findOrFail($valor);
                $solicitudId = $retiro->solicitud_id;

                // Cargamos la solicitud con sólo ese retiro filtrado dentro
                $solicitudes = Solicitud::visiblesSegunRol($esSolicitantePlanta, $esSolicitanteProductor)       // 🔒 Aplica scope de modelo Solicitud - filtro de visibilidad según el rol del usuario (planta, productor, o acceso total)
										->porRegionesOperativas($regionesOperativas)
                                        ->with([
                                                    'maquila.empresa:id,razon_social',
                                                    'maquila.sucursal:id,nombre_sucursal,comuna_id',
													'maquila.sucursal.comuna:id,region_id',
                                                    'usuario:id,nombre_usuario,apellidos_usuario',
                                                    'retiros' => function ($query) use ($valor) {
                                                                                                    $query
                                                                                                        ->where('id', $valor) // Filtra del retiro informado en el correo
                                                                                                        ->orderByDesc('id');
                                                                                                },
                                                    'retiros.tipoRetiro:id,nombre'
                                                ])
                                        ->where('id', $solicitudId)
                                        ->get(); // Una sola solicitud, la dueña del único retiro filtrado

                return view('solicitudes-retiro.index', compact('solicitudes', 'pivoteCorreo')); // Inyectamos pivote para que en el Blade muestre boton liberar pivote
                break;

            default:
                // Estamos en flujo normal desde menú lateral -> Extraemos todos los retiros de todas las solicitudes
                // Relevante es el orden de ID de la solicitud, y dentro de cada solicitud los retiros deben venir ordenados por su ID también

                $solicitudes = Solicitud::visiblesSegunRol($esSolicitantePlanta, $esSolicitanteProductor)       // 🔒 Aplica scope de modelo Solicitud - filtro de visibilidad según el rol del usuario (planta, productor, o acceso total)
										->porRegionesOperativas($regionesOperativas)
                                        ->with([
                                                    'maquila.empresa:id,razon_social',
                                                    'maquila.sucursal:id,nombre_sucursal,comuna_id',
													'maquila.sucursal.comuna:id,region_id',
                                                    'usuario:id,nombre_usuario,apellidos_usuario',
                                                    'retiros' => fn ($query) => $query->orderByDesc('id'),
                                                    'retiros.tipoRetiro:id,nombre',
                                                ])
                                        ->latest('id')
                                        ->get();

                return view('solicitudes-retiro.index', compact('solicitudes')); // NO inyectamos pivote para que en el Blade muestre los botones de filtros (caso normal)
                break;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();

        $usuario = [
            'id' => $user->id,
            'rol_id' => $user->rol_id,
            'empresa_id' => $user->empresa_id,
            'sucursal_id' => $user->sucursal_id,
        ];

        // Cargamos solo las relaciones necesarias y sus nombres
        $empresa = null;
        $sucursal = null;

        if ($usuario['empresa_id']) {
            $empresa = Empresa::find($usuario['empresa_id'], ['id', 'razon_social']);
        }

        if ($usuario['sucursal_id']) {
            $sucursal = Sucursal::find($usuario['sucursal_id'], ['id', 'nombre_sucursal']);
        }

        return view('solicitudes-retiro.create', [
            'usuario'  => (object) $usuario,     // lo pasamos como stdClass para mantener consistencia en blade
            'empresa'  => $empresa,
            'sucursal' => $sucursal,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // --- DEPURACIÓN: Log de campos hidden sincronizados desde el front ---
        // Log::info('- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ');
        // Log::info('🟦 [STORE SolicitudRetiro] Campos hidden recibidos');

        // $fechaRetiro          = $request->input('fecha_retiro', []);
        // $kilosEstimados       = $request->input('kilogramos_estimados', []);
        // $kilosEstimadosHidden = $request->input('kilogramos_estimados_hidden', []);
		// $requiereHidden       = $request->input('requiere_reposicion_hidden', []);
        // $binsHidden           = $request->input('cantidad_bins_hidden', []);
		// $tipoOperacionHidden  = $request->input('tipo_operacion_hidden', []);

        // Log::info('Array de kilogramos_estimados         :', $kilosEstimados);
        // Log::info('Array de kilogramos_estimados_hidden  :', $kilosEstimadosHidden);
        // Log::info('Array de requiere_reposicion_hidden   :', $requiereHidden);
        // Log::info('Array de cantidad_bins_hidden         :', $binsHidden);
        // Log::info('Array de tipo_operacion_hidden        :', $tipoOperacionHidden);

        // foreach ($fechaRetiro as $i => $valor) {
		// 	Log::info("🔍 Retiros[$i] → fecha_retiro                : " . ($fechaRetiro[$i] ?? 'null') );
		// 	Log::info("🔍 Retiros[$i] → kilogramos_estimados        : " . ($kilosEstimados[$i] ?? 'null') );
		// 	Log::info("🔍 Retiros[$i] → kilogramos_estimados_hidden : " . ($kilosEstimadosHidden[$i] ?? 'null') );
		// 	Log::info("🔍 Retiros[$i] → requiere_reposicion_hidden  : " . ($requiereHidden[$i] ?? 'null') );
		// 	Log::info("🔍 Retiros[$i] → cantidad_bins_hidden        : " . ($binsHidden[$i] ?? 'null') );
		// 	Log::info("🔍 Retiros[$i] → tipo_operacion_hidden       : " . ($tipoOperacionHidden[$i] ?? 'null') );
        // }

		// Resolver región operativa en backend.
		// La región de la solicitud la define la sucursal (planta).
		$request->validate([
			'sucursal_retiro' => ['required', 'exists:sucursales,id'],
		]);

		$sucursal = Sucursal::with('comuna')->findOrFail($request->input('sucursal_retiro'));
		$regionOperativa = $sucursal->comuna->region_id;

		$rulesBase = [
            'empresa_retiro'               => ['required', 'exists:empresas,id'],
            'sucursal_retiro'              => ['required', 'exists:sucursales,id'],

			'fecha_retiro'                 => ['required', 'array'],
            'fecha_retiro.*'               => ['required', 'date'],
			'tipo_retiro'                  => ['required', 'array'],
            'tipo_retiro.*'                => ['required', 'exists:catalogos,id'],

			'requiere_reposicion_hidden'   => ['required', 'array'],
			'requiere_reposicion_hidden.*' => ['required', 'in:0,1'],								// sólo 0 o 1, siempre llegan
			'cantidad_bins_hidden'         => ['required', 'array'],
			'cantidad_bins_hidden.*'       => ['nullable', 'integer', 'min:0'],
		];

		// Reglas de validación exclusivas de REGION_X
		// Kilogramos estimados debe venir
		if ($regionOperativa === config('constantes.REGION_X')) {
			// Kg NO pueden ser 0
			$rulesBase['kilogramos_estimados']   = ['required', 'array'];
			$rulesBase['kilogramos_estimados.*'] = ['required', 'numeric', 'min:1'];
		}

		// Reglas de validación exclusivas de REGION_XII
		// Validación de Tipo Retiro = Retiro BINs cuando Región Operativa es REGIÓN_XII
		if ($regionOperativa === config('constantes.REGION_XII')) {
			// Solo BINS
			$rulesBase['tipo_retiro.*'] = ['required', 'in:' . config('constantes.TIPO_RETIRO_BINS')];

			// Kg pueden ser 0
			$rulesBase['kilogramos_estimados_hidden']   = ['required', 'array'];
			$rulesBase['kilogramos_estimados_hidden.*'] = ['required', 'numeric', 'min:0'];

			$rulesBase['tipo_operacion_hidden']   = ['required', 'array'];
			$rulesBase['tipo_operacion_hidden.*'] = ['required', 'in:0,1'];
		}

		// Validar con $request (sin usar $data)
		$request->validate($rulesBase);

		// Reglas de validación exclusivas de REGION_X
		// Validación de kilogramos_estimados debe ser superior a cero (0).
		if ($regionOperativa === config('constantes.REGION_X')) {

			foreach ($request->kilogramos_estimados as $idx => $kilos) {
				if (empty($kilos) || (int) $kilos < 1) {
					throw ValidationException::withMessages([
						"kilogramos_estimados.$idx" => "Los kilogramos estimados debe ser al menos 1."
					]);
				}
			}

		}

		// Reglas de validación exclusivas de REGION_XII
		// Validación de kilogramos_estimados debe ser superior a cero (0) o tipo_operacion ha de ser REPOSICION.
		if ($regionOperativa === config('constantes.REGION_XII')) {
			foreach ($request->fecha_retiro as $idx => $fecha) {
				$kilos         = (float) ($request->kilogramos_estimados_hidden[$idx] ?? 0);
				$tipoOperacion = (int) ($request->tipo_operacion_hidden[$idx] ?? 0);

				// Log::info('IDX           = ' . $idx);
				// Log::info('kilos         = ' . $kilos );
				// Log::info('tipoOperacion = ' . $tipoOperacion );

				if ($tipoOperacion === config('constantes.TIPO_OPERACION_REPOSICION') && $kilos > 0) {
					throw ValidationException::withMessages([
						"kilogramos_estimados_hidden.$idx" => "Una reposición no puede tener kilogramos mayores a 0."
					]);
				}

				if ($tipoOperacion === config('constantes.TIPO_OPERACION_RETIRO') && $kilos <= 0) {
					throw ValidationException::withMessages([
						"tipo_operacion_hidden.$idx" => "Debe seleccionar Reposición cuando los kilogramos son 0."
					]);
				}
			}
		}

		// Validación de Cantidad de Bins condicional a que se Requiera Reposición.
		// Usando $request directamente como en el resto del código
		foreach ($request->requiere_reposicion_hidden as $idx => $requiere) {
			if ((int) $requiere === 1) {
				$cantidadBins = $request->cantidad_bins_hidden[$idx] ?? null;

				if (empty($cantidadBins) || (int) $cantidadBins < 1) {
					throw ValidationException::withMessages([
						"cantidad_bins_hidden.$idx" => "La cantidad de bins debe ser al menos 1 cuando se requiere reposición."
					]);
				}
			}
		}

        try {
            DB::beginTransaction();

            // 0. Obtener el id de la maquila
            $maquila = Maquila::where('empresa_id', $request->empresa_retiro)
                            ->where('sucursal_id', $request->sucursal_retiro)
                            ->firstOrFail();

            $request->merge(['maquila_id' => $maquila->id]);

            // 1. Crear la solicitud
            $solicitud = Solicitud::create([
                'usuario_id'      => Auth::user()->id,
                'maquila_id'      => $request->maquila_id, // ← este debe estar presente en el form
                'fecha_solicitud' => now(),
            ]);

            $hayParaHoy = false;
            $fechaHoy = Carbon::now()->toDateString(); // YYYY-MM-DD

            // 2. Crear retiros asociados
            foreach ($request->fecha_retiro as $index => $fecha) {

				switch ($regionOperativa) {
					case config('constantes.REGION_X'):
						$tipoOperacion = config('constantes.TIPO_OPERACION_RETIRO');
						$kilos         = (float) ($request->kilogramos_estimados[$index] ?? 0);
				        break;

					case config('constantes.REGION_XII'):
						$tipoOperacion = (int) ($request->tipo_operacion_hidden[$index] ?? 0);
						$kilos         = (float) ($request->kilogramos_estimados_hidden[$index] ?? 0);
				        break;

					default:
						throw ValidationException::withMessages([
							"$regionOperativa" => "La región operativa no es válida."
						]);
				}

				Retiro::create([
                    'solicitud_id'        => $solicitud->id,
                    'fecha_retiro'        => $fecha,
                    'tipo_retiro_id'      => $request->tipo_retiro[$index],

					'kilogramos_estimados'=> $kilos,
                    'requiere_reposicion' => $request->requiere_reposicion_hidden[$index] ? 1 : 0,
                    'cantidad_bins'       => $request->cantidad_bins_hidden[$index] ?? null,

					'tipo_operacion'      => $tipoOperacion,

                    'estado_id'           => config('constantes.ESTADO_RETIRO_ESPERANDO'), // ← inicial en estado esperando
                    'activo'              => true,
                ]);

                if (Carbon::parse($fecha)->toDateString() === $fechaHoy) {  // Marcamos que al menos un retiro se pidio para HOY
                    $hayParaHoy = true;
                }
            }

            DB::commit();

            // 3. Enviar correo al solicitante
            CorreoHelper::enviarCorreoSolicitudCreada($solicitud);

            if($hayParaHoy){                                                        //  Si detectamos que al menos hay un retiro solicitado para HOY se le avisa al Coordinador
                CorreoHelper::enviarCorreoSolicitudCreadaParaHoy($solicitud, $fechaHoy);
            }

            // 4. Redirigir con éxito
            return redirect()->route('solicitudes-retiro.index')
                            ->with('status', __('responses.solicitudes.store_success'));

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('❌ Error al grabar nueva solicitud de retiros', [
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withInput()
                        ->with('error', __('responses.solicitudes.store_error'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $retiro = Retiro::with([
                                'solicitud:id,maquila_id,usuario_id,created_at',
                                'solicitud.usuario:id,nombre_usuario,apellidos_usuario,rol_id',
                                'solicitud.usuario.rol:id,nombre',

								'solicitud.maquila.empresa:id,razon_social',
								'solicitud.maquila.sucursal:id,nombre_sucursal,comuna_id',
								'solicitud.maquila.sucursal.comuna:id,region_id', // ✅ AQUÍ

                                'tipoRetiro:id,nombre',
                                'estado:id,nombre',

                                'historial' => function ($query) {
                                                                    $query->select([
                                                                                    'id',
                                                                                    'retiro_id',
                                                                                    'fecha_retiro',
                                                                                    'tipo_retiro_id',
                                                                                    'kilogramos_estimados',
                                                                                    'requiere_reposicion',
                                                                                    'cantidad_bins',
                                                                                    'estado_id',
                                                                                    'usuario_id',
                                                                                    'motivo_cambio',
                                                                                    'created_at',
                                                                                ])
                                                                            ->with([
                                                                                    'tipoRetiro:id,nombre',
                                                                                    'estado:id,nombre',
                                                                                    'usuario:id,nombre_usuario,apellidos_usuario',
                                                                                    ])
                                                                            ->latest('created_at');
                                                                },

                                'comentarios' => function ($query) {
                                                                    $query->with([
                                                                                    'usuario:id,nombre_usuario,apellidos_usuario'
                                                                                ])
                                                                            ->orderByDesc('id');
                                                                },

                            ])->findOrFail($id, [
                                                    'id',
                                                    'solicitud_id',
                                                    'fecha_retiro',
                                                    'tipo_retiro_id',
                                                    'kilogramos_estimados',
                                                    'requiere_reposicion',
                                                    'cantidad_bins',
                                                    'estado_id',

                                                    'tipo_operacion',

                                                    'comentario_anulacion',
                            ]);

        // Formato de fecha y fecha y hora para el registro del Retiro
        $retiro->solicitud->created_at_format = $retiro->solicitud->created_at->format('Y-m-d');
        $retiro->fecha_retiro_format = $retiro->fecha_retiro->format('Y-m-d H:i'); // Casteado como datetime en el modelo Retiro

        // Formato de fecha y hora para cada registro del Historial del Retiro
        $retiro->historial->each(function ($registro) {
            $registro->fecha_retiro_format = $registro->fecha_retiro->format('Y-m-d H:i');  // Casteado como datetime en el modelo del historial del Retiro
            $registro->created_at_format = $registro->created_at->format('Y-m-d H:i');  // Casteado como datetime en el modelo del historial del Retiro

            // 👇 FORZAMOS EL ACCESO A LA RELACIÓN para evitar omisión en JSON
            $registro->tipoRetiro?->nombre;
            $registro->estado?->nombre;
            $registro->usuario?->nombre_usuario;
        });

        // Formato de fecha y hora para cada registro de Comentarios del Retiro
        $retiro->comentarios->each(function ($comentario) {
            $comentario->created_at_format = $comentario->created_at->format('Y-m-d H:i');

            // 👇 FORZAMOS EL ACCESO A LA RELACIÓN para evitar omisión en JSON
            $comentario->usuario?->nombre_usuario;
        });

        return response()->json($retiro);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = Auth::user();

        $usuario = [
            'id'          => $user->id,
            'rol_id'      => $user->rol_id,
            'empresa_id'  => $user->empresa_id,
            'sucursal_id' => $user->sucursal_id,
        ];

        $retiro_id = Crypt::decrypt($id);

        $retiro = Retiro::with([
                                'solicitud:id,maquila_id,usuario_id,created_at',
                                'solicitud.usuario:id,nombre_usuario,apellidos_usuario,rol_id',
                                'solicitud.usuario.rol:id,nombre',

								'solicitud.maquila.empresa:id,razon_social',
								'solicitud.maquila.sucursal:id,nombre_sucursal,comuna_id',
								'solicitud.maquila.sucursal.comuna:id,region_id', // ✅ AQUÍ

                                'tipoRetiro:id,nombre',
                                'estado:id,nombre',

                                // 👇 Cargamos el historial con relaciones necesarias
                                'historial' => function ($query) {
                                                                    $query->select([
                                                                                    'id',
                                                                                    'retiro_id',
                                                                                    'fecha_retiro',
                                                                                    'tipo_retiro_id',
                                                                                    'kilogramos_estimados',
                                                                                    'requiere_reposicion',
                                                                                    'cantidad_bins',
                                                                                    'estado_id',
                                                                                    'usuario_id',
                                                                                    'motivo_cambio',
                                                                                    'created_at',
                                                                                ])
                                                                            ->with([
                                                                                'tipoRetiro:id,nombre',
                                                                                'estado:id,nombre',
                                                                                'usuario:id,nombre_usuario,apellidos_usuario',
                                                                            ])
                                                                            ->latest('created_at');
                                                                },

                                // 👇 Cargamos los comentarios descendentes, con el usuario
                                'comentarios' => function ($query) {
                                                                    $query->with(['usuario:id,nombre_usuario,apellidos_usuario'])
                                                                        ->orderByDesc('id');
                                                                }
                            ])->findOrFail($retiro_id);

        // Formatear fecha del comentario para la vista (igual que con historial)
        $retiro->comentarios->each(function ($comentario) {
            $comentario->created_at_format = $comentario->created_at->format('Y-m-d H:i');

            // 👇 FORZAMOS EL ACCESO A LA RELACIÓN para evitar omisión en JSON
            $comentario->usuario?->nombre_usuario;
        });

        // Formato para visualización en tabla
        $retiro->historial->each(function ($registro) {
            $registro->fecha_retiro_format = $registro->fecha_retiro->format('Y-m-d H:i');
            $registro->created_at_format = $registro->created_at->format('Y-m-d H:i');

            // 👇 FORZAMOS EL ACCESO A LA RELACIÓN para evitar omisión en JSON
            $registro->tipoRetiro?->nombre;
            $registro->estado?->nombre;
            $registro->usuario?->nombre_usuario;
        });

        $empresa  = $retiro->solicitud->maquila->empresa;
        $sucursal = $retiro->solicitud->maquila->sucursal;

        return view('solicitudes-retiro.edit', [
            'usuario'       => (object) $usuario,
            'empresa'       => $empresa,
            'sucursal'      => $sucursal,
            'retiro'        => $retiro,
            'pivoteCorreo'  => session('pivoteCorreo'), // Inyectamos pivoteCorreo solo si existe
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // --- DEPURACIÓN: Log de campos hidden sincronizados desde el front ---
        // Log::info('- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ');
        // Log::info('🟦 [UPDATE SolicitudRetiro] Campos hidden recibidos');

        // $fechaRetiro          = $request->input('fecha_retiro', []);
        // $kilosEstimados       = $request->input('kilogramos_estimados', []);
        // $kilosEstimadosHidden = $request->input('kilogramos_estimados_hidden', []);
		// $requiereHidden       = $request->input('requiere_reposicion_hidden', []);
        // $binsHidden           = $request->input('cantidad_bins_hidden', []);
		// $tipoOperacionHidden  = $request->input('tipo_operacion_hidden', []);

        // Log::info('Array de kilogramos_estimados         :', $kilosEstimados);
        // Log::info('Array de kilogramos_estimados_hidden  :', $kilosEstimadosHidden);
        // Log::info('Array de requiere_reposicion_hidden   :', $requiereHidden);
        // Log::info('Array de cantidad_bins_hidden         :', $binsHidden);
        // Log::info('Array de tipo_operacion_hidden        :', $tipoOperacionHidden);

        // foreach ($fechaRetiro as $i => $valor) {
		// 	Log::info("🔍 Retiros[$i] → fecha_retiro                : " . ($fechaRetiro[$i] ?? 'null') );
		// 	Log::info("🔍 Retiros[$i] → kilogramos_estimados        : " . ($kilosEstimados[$i] ?? 'null') );
		// 	Log::info("🔍 Retiros[$i] → kilogramos_estimados_hidden : " . ($kilosEstimadosHidden[$i] ?? 'null') );
		// 	Log::info("🔍 Retiros[$i] → requiere_reposicion_hidden  : " . ($requiereHidden[$i] ?? 'null') );
		// 	Log::info("🔍 Retiros[$i] → cantidad_bins_hidden        : " . ($binsHidden[$i] ?? 'null') );
		// 	Log::info("🔍 Retiros[$i] → tipo_operacion_hidden       : " . ($tipoOperacionHidden[$i] ?? 'null') );
        // }

        // 🔁 Restauramos el pivote si vino desde el form
        if ($request->filled('pivoteCorreo')) {
            session(['pivoteCorreo' => json_decode($request->input('pivoteCorreo'), true)]);
        }

		$retiro = Retiro::with('solicitud.maquila.sucursal.comuna')->findOrFail($id);
		$regionOperativa = $retiro->solicitud->region_operativa_id;		// Mejor usamos accesor del modelo (Los controladores orquestan y los modelos saben).

		$rulesBase = [
			'fecha_retiro'                  => ['required', 'array'],
			'fecha_retiro.*'                => ['required', 'date'],
			'tipo_retiro'                   => ['required', 'array'],
			'tipo_retiro.*'                 => ['required', 'exists:catalogos,id'], // en store lo pasaste a integer
			'requiere_reposicion_hidden'    => ['required', 'array'],
			'requiere_reposicion_hidden.*'  => ['required', 'in:0,1'],
			'cantidad_bins_hidden'          => ['required', 'array'],
			'cantidad_bins_hidden.*'        => ['nullable', 'integer', 'min:0'],
			'comentario'                    => ['required', 'string', 'min:5'],
		];


		// Reglas de validación exclusivas de REGION_X
		// Kilogramos estimados debe venir
		if ($regionOperativa === config('constantes.REGION_X')) {
			// Kg NO pueden ser 0
			$rulesBase['kilogramos_estimados']   = ['required', 'array'];
			$rulesBase['kilogramos_estimados.*'] = ['required', 'numeric', 'min:1'];
		}

		// Reglas de validación exclusivas de REGION_XII
		// Validación de Tipo Retiro = Retiro BINs cuando Región Operativa es REGIÓN_XII
		if ($regionOperativa === config('constantes.REGION_XII')) {
			// Solo BINS
			$rulesBase['tipo_retiro.*'] = ['required', 'in:' . config('constantes.TIPO_RETIRO_BINS')];

			// Kg pueden ser 0
			$rulesBase['kilogramos_estimados_hidden']   = ['required', 'array'];
			$rulesBase['kilogramos_estimados_hidden.*'] = ['required', 'numeric', 'min:0'];

			$rulesBase['tipo_operacion_hidden']   = ['required', 'array'];
			$rulesBase['tipo_operacion_hidden.*'] = ['required', 'in:0,1'];
		}


		// Validar con $request (sin usar $data)
		$request->validate($rulesBase);

		// Reglas de validación exclusivas de REGION_X
		// Validación de kilogramos_estimados debe ser superior a cero (0).
		if ($regionOperativa === config('constantes.REGION_X')) {

			foreach ($request->kilogramos_estimados as $idx => $kilos) {
				if (empty($kilos) || (int) $kilos < 1) {
					throw ValidationException::withMessages([
						"kilogramos_estimados.$idx" => "Los kilogramos estimados debe ser al menos 1."
					]);
				}
			}

		}

		// Reglas de validación exclusivas de REGION_XII
		// Validación de kilogramos_estimados debe ser superior a cero (0) o tipo_operacion ha de ser REPOSICION.
		if ($regionOperativa === config('constantes.REGION_XII')) {
			foreach ($request->fecha_retiro as $idx => $fecha) {
				$kilos         = (float) ($request->kilogramos_estimados_hidden[$idx] ?? 0);
				$tipoOperacion = (int) ($request->tipo_operacion_hidden[$idx] ?? 0);

				// Log::info('IDX           = ' . $idx);
				// Log::info('kilos         = ' . $kilos );
				// Log::info('tipoOperacion = ' . $tipoOperacion );

				if ($tipoOperacion === config('constantes.TIPO_OPERACION_REPOSICION') && $kilos > 0) {
					throw ValidationException::withMessages([
						"kilogramos_estimados_hidden.$idx" => "Una reposición no puede tener kilogramos mayores a 0."
					]);
				}

				if ($tipoOperacion === config('constantes.TIPO_OPERACION_RETIRO') && $kilos <= 0) {
					throw ValidationException::withMessages([
						"tipo_operacion_hidden.$idx" => "Debe seleccionar Reposición cuando los kilogramos son 0."
					]);
				}
			}
		}


		// Validación de Cantidad de Bins condicional a que se Requiera Reposición.
		// Usando $request directamente como en el resto del código
		foreach ($request->requiere_reposicion_hidden as $idx => $requiere) {
			if ((int) $requiere === 1) {
				$cantidadBins = $request->cantidad_bins_hidden[$idx] ?? null;

				if (empty($cantidadBins) || (int) $cantidadBins < 1) {
					throw ValidationException::withMessages([
						"cantidad_bins_hidden.$idx" => "La cantidad de bins debe ser al menos 1 cuando se requiere reposición."
					]);
				}
			}
		}

        try {
            // $retiro = Retiro::findOrFail($id);

            // Al guardar el historial usamos el comentario para ponerlo en el motivo de cambio del historial, usamos método definido en modelo retiro.
            $comentario = $request->input('comentario');
            $retiro->guardarHistorial($comentario);


			switch ($regionOperativa) {
				case config('constantes.REGION_X'):
					$tipoOperacion = config('constantes.TIPO_OPERACION_RETIRO');
					$kilos         = (float) ($request->kilogramos_estimados[0] ?? 0);
					break;

				case config('constantes.REGION_XII'):
					$tipoOperacion = (int) ($request->tipo_operacion_hidden[0] ?? 0);
					$kilos         = (float) ($request->kilogramos_estimados_hidden[0] ?? 0);
					break;

				default:
					throw ValidationException::withMessages([
						"$regionOperativa" => "La región operativa no es válida."
					]);
			}

            $retiro->update([
                            'fecha_retiro'        => $request->fecha_retiro[0],
                            'tipo_retiro_id'      => $request->tipo_retiro[0],

							'kilogramos_estimados'=> $kilos,
                            'requiere_reposicion' => $request->requiere_reposicion_hidden[0] ? 1 : 0,
                            'cantidad_bins'       => $request->cantidad_bins_hidden[0] ?? null,

							'tipo_operacion'      => $tipoOperacion,
            ]);

            $retiro->guardarComentario($comentario);

            // Este método UPDATE debe ser sa activado sólo por el dueño de la solicitud, es decir, quien la creo .
            // Si un Coordinador o Admin-IT quiere comentar la solicitud debe hacerlo vía EVALUAR -> Comentar.
            // Dejamos el bloque de código como vestigio y en caso de que solicitaran el cambio.
                // if ( in_array(Auth::user()->rol_id, [   config('constantes.ROL_COORDINADOR'),
                //                                         config('constantes.ROL_ADMINISTRADOR_IT') ]) ) {

                //     // Si quien comenta es Coordinador o Admin-IT marcamos el retiro como COMENTADO
                //     $retiro->estado_id = config('constantes.ESTADO_RETIRO_COMENTADO');
                //     $retiro->save();

                //     // Enviar correo al solicitante con la notificación del comentario del coordinador
                //     CorreoHelper::enviarCorreoRetiroComentadoPorCoordinador($retiro);
                // }
                // else{
                //     // Enviar correo al coordinador con la notificación del comentario del solicitante
                //     CorreoHelper::enviarCorreoRetiroComentadoPorSolicitante($retiro);
                // }
            // Si la solicitud hubiese sido creada por un COORDINADOR o un ADMIN-IT le va a llegar un correo del solicitante que son ellos mismos.
            // Pero solicitaron que la creación de solicitudes no estuviera disponible para COORDINADORES y ADMIN-IT.
            // Si ellos quiere crear solicitud lo pueden hacer desde la Manual.

            // Enviar correo al coordinador con la notificación del comentario del solicitante
            CorreoHelper::enviarCorreoRetiroComentadoPorSolicitante($retiro);

            return redirect()->route('solicitudes-retiro.index')
                            ->with('status', __('responses.solicitudes.update_success'));

        } catch (\Throwable $e) {
            Log::error('❌ Error al actualizar retiro', [
                'id'    => $id,
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withInput()
                        ->with('error', __('responses.solicitudes.update_error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // 🔁 Restauramos el pivote si vino desde el form
        if ($request->filled('pivoteCorreo')) {
            session(['pivoteCorreo' => json_decode($request->input('pivoteCorreo'), true)]);
        }

        try {
            $retiro = Retiro::findOrFail($id);

            $retiro->activo = false; // Marcamos como inactivo
            $retiro->estado_id = config('constantes.ESTADO_RETIRO_CANCELADO');
            $retiro->comentario_anulacion = $request->comentario_anulacion;
            $retiro->save();

            return redirect()
                    ->route('solicitudes-retiro.index')
                    ->with('status', __('responses.solicitudes.delete_success'));

        } catch (\Throwable $e) {
            Log::error('❌ Error al eliminar retiro', [
                'id'    => $id,
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                    ->withInput()
                    ->with('error', __('responses.solicitudes.delete_error'));
        }
    }

    /**
     * Receive and save de remarks to the retiro.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function comentarRetiro(Request $request, $id)
    {
        $request->validate([
            'comentario' => ['required', 'string', 'max:1000'],
        ]);

        try {
            // RetiroComentario::create([
            //     'retiro_id' => $id,
            //     'usuario_id' => Auth::id(),
            //     'comentario' => $request->comentario,
            //     'created_at' => now(),
            // ]);

            $retiro = Retiro::findOrFail($id);
            $retiro->guardarComentario($request->comentario);

            // Este comentario es de Coordinador o Admin-IT por que repasa el estado de la solicitud de retiro
            $retiro->estado_id = config('constantes.ESTADO_RETIRO_COMENTADO');
            $retiro->save();

            // Enviar correo al solicitante con la notificación del comentario del coordinador
            CorreoHelper::enviarCorreoRetiroComentadoPorCoordinador($retiro);

            return redirect()->route('solicitudes-retiro.index')->with('status', __('responses.solicitudes.comentario_guardado'));

        } catch (\Throwable $e) {
            Log::error('❌ Error al guardar comentario en retiro', [
                'id'    => $id,
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('solicitudes-retiro.index')->with('error', __('responses.solicitudes.comentario_error'));
        }
    }

    /**
     * Aprove the retiro.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function aprobarRetiro($id)
    {
        try {
            $retiro = Retiro::findOrFail($id);
            $retiro->estado_id = config('constantes.ESTADO_RETIRO_ACEPTADO');
            $retiro->save();

            // Crear la planificación inicial (registro vacío) asociada al retiro
            $retiro->crearPlanificacionInicial();

            // Enviar correo al solicitante con la notificación de la aprobación del retiro
            CorreoHelper::enviarCorreoRetiroAprobado($retiro);

            return redirect()
                ->route('solicitudes-retiro.index')
                ->with('status', __('responses.solicitudes.retiro_aprobado'));

        } catch (\Throwable $e) {
            Log::error('❌ Error al aprobar retiro', [
                'id'    => $id,
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->route('solicitudes-retiro.index')
                ->with('error', __('responses.solicitudes.retiro_aprobado_error'));
        }
    }

    /**
     * Ruta intermedia para recibir requierimiento desde correo de notficación de creación de la solicitud.
     * Sirve para ocultar TOKEN y no aparezca a mostrar el index.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function verDesdeToken($token)
    {
        try {
            $pivoteCorreo = Crypt::decrypt($token); // es un array: ['campo' => 'solicitud_id', 'valor' => 42] y quizás ['fechaHoy' => $fechaHoy] (en caso de notificar Creación para HOY)
            session()->put('pivoteCorreo', $pivoteCorreo);

            return redirect()->route('solicitudes-retiro.index'); // Redirije al index pero con URL limpia y pivto en sesión
        }
        catch (\Exception $e) { // Token inválido o manipulado
            Log::error('❌ Error al ver desde correo retiro', [
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('solicitudes-retiro.index')->with('error', __('emails.token_invalido_expirado'));
        }
    }

    public function data(Request $request) {

    }
}
