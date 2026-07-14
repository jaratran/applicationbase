<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

use \Illuminate\Validation\ValidationException;

use Carbon\Carbon;

use App\Models\Solicitud;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Sucursal;
use App\Models\Maquila;
use App\Models\Retiro;
use App\Models\Planificacion;

use App\Helpers\CorreoHelper;

class PlanificacionesRetiroController extends Controller
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
            return redirect()->route('planificaciones-retiro.index');
        }

        // 🔍 Si petición tuvo origen en correo de notificación de creación de solicitud y no eliminamos el pivote de contexto
        $pivoteCorreo = session('pivoteCorreo'); // nombre claro, genérico y expresivo
        if ($pivoteCorreo) {
            $campo = $pivoteCorreo['campo'] ?? 'ninguno';
            $valor = $pivoteCorreo['valor'] ?? null;
        }
        else{
            $campo = 'ninguno';
            $valor = null;
        }

        // Establecemos rol del usuario de la sesión y si es solicitante planta o solicitante productor.
        $rolSesion              = Auth::user()->rol_id;
        $esSolicitantePlanta    = $rolSesion === config('constantes.ROL_SOLICITANTE_PLANTA');
        $esSolicitanteProductor = $rolSesion === config('constantes.ROL_SOLICITANTE_PRODUCTOR');
		$regionesOperativas     = Auth::user()->regiones_operativas_ids;

        if($campo === 'retiro_id' ){
            // Cargamos todas las solicitudes con su maquila, usuario y sus retiros ordenados
            $retiros = Retiro::with([
                                        'solicitud.maquila.empresa:id,razon_social',
                                        'solicitud.maquila.sucursal:id,nombre_sucursal,comuna_id',
										'solicitud.maquila.sucursal.comuna:id,region_id',
                                        'solicitud.usuario:id,nombre_usuario,apellidos_usuario',
                                        'tipoRetiro:id,nombre',
                                        'planificacion' => function ($query) {
                                                                                $query->with([
                                                                                    'especie:id,nombre',
                                                                                    'tipoMateriaPrima:id,nombre',
                                                                                    'estado:id,nombre',
                                                                                    'camion:id,patente,empresa_id,tipo_camion_id',
                                                                                    'camion.empresa:id,razon_social',
                                                                                    'camion.tipoCamion:id,nombre',
                                                                                    'conductor:id,nombre,apellido',
																					'rampla:id,patente',				// ✅ ESTA ERA LA PIEZA FALTANTE
                                                                                ]);
                                                                            }
                                    ])
                                    ->where('id', $valor)                                                                       // Sólo el retiro aludido

                                    // 🔒 Aplica scope de modelo Solicitud - filtro de visibilidad según el rol del usuario (planta, productor, o acceso total)
                                    ->whereHas('solicitud', function ($q) use ($esSolicitantePlanta, $esSolicitanteProductor) {
                                                                $q->visiblesSegunRol($esSolicitantePlanta, $esSolicitanteProductor);
                                                            })
									// 🌎 NUEVO: cerco por región operativa
									->porRegionesOperativas($regionesOperativas)

                                    ->get();

            return view('planificaciones-retiro.index', compact('retiros', 'pivoteCorreo'));

        }
        else{
            // Cargamos todas las solicitudes con su maquila, usuario y sus retiros ordenados
            $retiros = Retiro::with([
                                        'solicitud.maquila.empresa:id,razon_social',
                                        'solicitud.maquila.sucursal:id,nombre_sucursal,comuna_id',
										'solicitud.maquila.sucursal.comuna:id,region_id',
                                        'solicitud.usuario:id,nombre_usuario,apellidos_usuario',
                                        'tipoRetiro:id,nombre',
                                        'planificacion' => function ($query) {
                                                                                $query->with([
                                                                                    'especie:id,nombre',
                                                                                    'tipoMateriaPrima:id,nombre',
                                                                                    'estado:id,nombre',
                                                                                    'camion:id,patente,empresa_id,tipo_camion_id',
                                                                                    'camion.empresa:id,razon_social',
                                                                                    'camion.tipoCamion:id,nombre',
                                                                                    'conductor:id,nombre,apellido',
																					'rampla:id,patente',				// ✅ ESTA ERA LA PIEZA FALTANTE
                                                                                ]);
                                                                            }
                                    ])

                                    ->whereIn('estado_id', [                                // Sólo retiros en estos estados
                                                                config('constantes.ESTADO_RETIRO_ACEPTADO'),
                                                                config('constantes.ESTADO_RETIRO_PLANIFICADO'),
                                                                config('constantes.ESTADO_RETIRO_PROGRAMADO'),
                                                                config('constantes.ESTADO_RETIRO_TERMINADO'),
                                                                config('constantes.ESTADO_RETIRO_CANCELADO'),
                                                            ])

                                    ->where(function ($query) {                             // Que omita los retiros CANCELADOS que no alcanzaron a tener Planificación
                                                                $query->where('estado_id', '!=', config('constantes.ESTADO_RETIRO_CANCELADO'))
                                                                        ->orWhereHas('planificacion');
                                                            })

                                    // 🔒 Aplica scope de modelo Solicitud - filtro de visibilidad según el rol del usuario (planta, productor, o acceso total)
                                    ->whereHas('solicitud', function ($q) use ($esSolicitantePlanta, $esSolicitanteProductor) {
                                                                $q->visiblesSegunRol($esSolicitantePlanta, $esSolicitanteProductor);
                                                            })
									// 🌎 NUEVO: cerco por región operativa
									->porRegionesOperativas($regionesOperativas)

                                    ->latest('id')
                                    ->get();

            return view('planificaciones-retiro.index', compact('retiros'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createManual()
    {
        $usuario = User::with([
                                'rol:id,nombre',
                                'empresa:id,razon_social',
                                'sucursal:id,nombre_sucursal',
                                'comuna:id,nombre,region_id',
                                'comuna.region:id,nombre'
                            ])
                            ->where("id", Auth::user()->id)
                            ->first([
                                'id',
                                'nombre_usuario',
                                'apellidos_usuario',
                                'empresa_id',
                                'sucursal_id',
                                'rol_id'
                            ]);

        // Cargamos solo las relaciones necesarias y sus nombres
        $empresa = null;
        $sucursal = null;

        if ($usuario['empresa_id']) {
            $empresa = Empresa::find($usuario['empresa_id'], ['id', 'razon_social']);
        }

        if ($usuario['sucursal_id']) {
            $sucursal = Sucursal::find($usuario['sucursal_id'], ['id', 'nombre_sucursal']);
        }

        return view('planificaciones-retiro.create-manual', [
            'usuario'  => $usuario,
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
    public function storeManual(Request $request)
    {
        // --- DEPURACIÓN: Log de campos hidden sincronizados desde el front ---
        // Log::info('- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ');
        // Log::info('🟦 [STORE SolicitudPlanificacion] Campos hidden recibidos');

        // $fechaRetiro          = $request->input('fecha_retiro', []);
        // $kilosEstimados       = $request->input('kilogramos_estimados', []);
        // $kilosEstimadosHidden = $request->input('kilogramos_estimados_hidden', []);
		// $requiereHidden       = $request->input('requiere_reposicion_hidden', []);
        // $binsHidden           = $request->input('cantidad_bins_hidden', []);
		// $tipoOperacionHidden  = $request->input('tipo_operacion_hidden', []);

		// $fechaPlanificada     = $request->input('fecha_planificada', []);
        // $tieneRestriccion     = $request->input('tiene_restriccion_hidden', []);
        // $fechaEmbarque        = $request->input('fecha_embarque', []);
        // $fechaArriboPuerto    = $request->input('fecha_arribo_puerto', []);
        // $fechaRescatePuerto   = $request->input('fecha_rescate_puerto', []);

        // Log::info('Array de fecha_retiro                 :', $fechaRetiro);
        // Log::info('Array de kilogramos_estimados         :', $kilosEstimados);
        // Log::info('Array de kilogramos_estimados_hidden  :', $kilosEstimadosHidden);
        // Log::info('Array de requiere_reposicion_hidden   :', $requiereHidden);
        // Log::info('Array de cantidad_bins_hidden         :', $binsHidden);
        // Log::info('Array de tipo_operacion_hidden        :', $tipoOperacionHidden);

		// Log::info('Array de fecha_planificada            :', $fechaPlanificada);
		// Log::info('Array de tiene_restriccion_hidden     :', $tieneRestriccion);
        // Log::info('Array de fecha_embarque               :', $fechaEmbarque);
        // Log::info('Array de fecha_arribo_puerto          :', $fechaArriboPuerto);
        // Log::info('Array de fecha_rescate_puerto         :', $fechaRescatePuerto);

        // foreach ($fechaRetiro as $i => $valor) {
		// 	Log::info("🔍 Sol-Retiro[$i] → fecha_retiro                : " . ($fechaRetiro[$i] ?? 'null') );
		// 	Log::info("🔍 Sol-Retiro[$i] → kilogramos_estimados        : " . ($kilosEstimados[$i] ?? 'null') );
		// 	Log::info("🔍 Sol-Retiro[$i] → kilogramos_estimados_hidden : " . ($kilosEstimadosHidden[$i] ?? 'null') );
		// 	Log::info("🔍 Sol-Retiro[$i] → requiere_reposicion_hidden  : " . ($requiereHidden[$i] ?? 'null') );
		// 	Log::info("🔍 Sol-Retiro[$i] → cantidad_bins_hidden        : " . ($binsHidden[$i] ?? 'null') );
		// 	Log::info("🔍 Sol-Retiro[$i] → tipo_operacion_hidden       : " . ($tipoOperacionHidden[$i] ?? 'null') );

		// 	Log::info("🔍 Sol-Planif[$i] → fecha_planificada           : " . ($fechaPlanificada[$i] ?? 'null') );
		// 	Log::info("🔍 Sol-Planif[$i] → tiene_restriccion_hidden    : " . ($tieneRestriccion[$i] ?? 'null') );
		// 	Log::info("🔍 Sol-Planif[$i] → fecha_embarque              : " . ($fechaEmbarque[$i] ?? 'null') );
		// 	Log::info("🔍 Sol-Planif[$i] → fecha_arribo_puerto         : " . ($fechaArriboPuerto[$i] ?? 'null') );
		// 	Log::info("🔍 Sol-Planif[$i] → fecha_rescate_puerto        : " . ($fechaRescatePuerto[$i] ?? 'null') );
        // }

		// ---------------------------------------------------------------------
		// Resolver región operativa en backend (fuente de verdad)
		// La región la define la sucursal (planta), NO el frontend
		// ---------------------------------------------------------------------
		$request->validate([
			'sucursal_retiro' => ['required', 'exists:sucursales,id'],
		]);

		$sucursal        = Sucursal::with('comuna')->findOrFail($request->input('sucursal_retiro'));
		$regionOperativa = $sucursal->comuna->region_id;

		// Protección para no procesar una región nueva sin pasar por planificación.
		// Esto evita tener que poner explicitos default en los siguientes switch-case de validacion y persistencia
		// Resguarda corrupción silenciosa de datos, sucursal con región operativa no integrada a la lógica
		if (!in_array($regionOperativa, [ config('constantes.REGION_X'), config('constantes.REGION_XII') ])) {
			throw new \LogicException('Región operativa no soportada');
		}

		// VALIDACION SECCIÓN SOLICITUD
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
		$dataSol = $request->validate($rulesBase);

		// Reglas de validación exclusivas de REGION_X
		// Validación de kilogramos_estimados debe ser superior a cero (0).
		if ($regionOperativa === config('constantes.REGION_X')) {

			foreach ($dataSol['kilogramos_estimados'] as $idx => $kilos) {
				if ($kilos === null || (int) $kilos < 1) {
					throw ValidationException::withMessages([
						"kilogramos_estimados.$idx" => "Los kilogramos estimados debe ser al menos 1."
					]);
				}
			}

		}

		// Reglas de validación exclusivas de REGION_XII
		// Validación de kilogramos_estimados debe ser superior a cero (0) o tipo_operacion ha de ser REPOSICION.
		if ($regionOperativa === config('constantes.REGION_XII')) {

			foreach ($dataSol['fecha_retiro'] as $idx => $fecha) {
				$kilos         = (float) ($dataSol['kilogramos_estimados_hidden'][$idx] ?? 0);
				$tipoOperacion = (int)   ($dataSol['tipo_operacion_hidden'][$idx] ?? 0);

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

		// Reglas para ambos Contextos Regionales (X y XII).
		// Validación de Cantidad de Bins condicional a que se Requiera Reposición.
		// Usando $request directamente como en el resto del código
		foreach ($dataSol['requiere_reposicion_hidden'] as $idx => $requiere) {

			if ((int) $requiere === 1) {
				$cantidadBins = $dataSol['cantidad_bins_hidden'][$idx] ?? null;

				if (!is_numeric($cantidadBins) || (int) $cantidadBins < 1) {
					throw ValidationException::withMessages([
						"cantidad_bins_hidden.$idx" => "La cantidad de bins debe ser al menos 1 cuando se requiere reposición."
					]);
				}
			}
		}

		// VALIDACION SECCIÓN PLANIFICACIÓN
		// Reglas específicas según región operativa
		$rulesPlanificacion = [];
		switch ($regionOperativa) {
			case config('constantes.REGION_X'):														// Región X (modelo tradicional)

				$rulesPlanificacion = [
					'fecha_planificada'              => ['required', 'array'],
					'fecha_planificada.*'            => ['required', 'date'],

					'duracion_viaje'                 => ['required', 'array'],
					'duracion_viaje.*'               => ['required', 'regex:/^\d{2}:\d{2}$/'],

					'hora_llegada_estimada_hidden'   => ['required', 'array'],
					'hora_llegada_estimada_hidden.*' => ['required', 'date'],

					'tipo_materia_prima'             => ['required', 'array'],
					'tipo_materia_prima.*'           => ['required', 'exists:catalogos,id'],

					'especie'                        => ['required', 'array'],
					'especie.*'                      => ['required', 'exists:catalogos,id'],

					'tiene_restriccion_hidden'       => ['required', 'array'],
					'tiene_restriccion_hidden.*'     => ['required', 'in:0,1'],

					'patente_camion'                 => ['required', 'array'],
					'patente_camion.*'               => ['required', 'exists:camiones,id'],

					'conductor'                      => ['required', 'array'],
					'conductor.*'                    => ['required', 'exists:conductores,id'],

					'patente_rampla'                 => ['nullable', 'array'],
					'patente_rampla.*'               => ['nullable', 'string', 'max:20'],
				];

				break;

			case config('constantes.REGION_XII'):													// Región XII (Rampla / Cabotaje)

				$rulesPlanificacion = [
					'fecha_planificada'          => ['required', 'array'],
					'fecha_planificada.*'        => ['required', 'date'],

					'duracion_estimada_dias'     => ['nullable', 'array'],
					'duracion_estimada_dias.*'   => ['nullable', 'integer', 'min:0'],

					'eta_calculada_hidden'       => ['required', 'array'],
					'eta_calculada_hidden.*'     => ['required', 'date'],

					'tipo_materia_prima'         => ['nullable', 'array'],
					'tipo_materia_prima.*'       => ['nullable', 'exists:catalogos,id'],

					'especie'                    => ['nullable', 'array'],
					'especie.*'                  => ['nullable', 'exists:catalogos,id'],

					'tiene_restriccion_hidden'   => ['required', 'array'],
					'tiene_restriccion_hidden.*' => ['required', 'in:0,1'],

					'tipo_transporte'            => ['nullable', 'array'],
					'tipo_transporte.*'          => ['nullable', 'exists:catalogos,id'],

					'fecha_embarque'             => ['nullable', 'array'],
					'fecha_embarque.*'           => ['nullable', 'date'],

					'fecha_arribo_puerto'        => ['nullable', 'array'],
					'fecha_arribo_puerto.*'      => ['nullable', 'date'],

					'patente_rampla'             => ['nullable', 'array'],
					'patente_rampla.*'           => ['nullable', 'exists:ramplas,id'],

					'estado_rampla'              => ['nullable', 'array'],
					'estado_rampla.*'            => ['nullable', 'exists:catalogos,id'],

					'patente_camion'             => ['nullable', 'array'],
					'patente_camion.*'           => ['nullable', 'exists:camiones,id'],

					'conductor'                  => ['nullable', 'array'],
					'conductor.*'                => ['nullable', 'exists:conductores,id'],

					'fecha_rescate_puerto'       => ['nullable', 'array'],
					'fecha_rescate_puerto.*'     => ['nullable', 'date'],

					'camion_rescate'             => ['nullable', 'array'],
					'camion_rescate.*'           => ['nullable', 'exists:camiones,id'],

					'conductor_rescate'          => ['nullable', 'array'],
					'conductor_rescate.*'        => ['nullable', 'exists:conductores,id'],
				];

				break;
		}

		// Validación final consolidada
		$dataPlan = $request->validate($rulesPlanificacion);

		// Validación propias de la REGION XII pero sólo cuando es RETIRO
		// 1) Existencia de aquellos campos requeridos en RETIRO pero nulos en REPOSICION
		// 2) Existencia de fechas de cabotaje y datos requeridos en transportes maritimos
		if ($regionOperativa === config('constantes.REGION_XII')) {
			foreach ($dataPlan['fecha_planificada'] as $idx => $fecha) {
				$tipoOperacion = (int)   ($dataSol['tipo_operacion_hidden'][$idx] ?? 0);

				if ($tipoOperacion === config('constantes.TIPO_OPERACION_RETIRO')) {

					// EXISTENCIA DE CAMPOS REQUERIDOS POR RETIROS - Si se trata de REPOSICION estos campos pueden y deben ser NULOS
					$duracionEstimadaDias = data_get($dataPlan, "duracion_estimada_dias.$idx");
					if (!is_numeric($duracionEstimadaDias) || (int) $duracionEstimadaDias < 1) {
						throw ValidationException::withMessages([
							"duracion_estimada_dias.$idx" => "El campo duracion estimada dias es obligatorio."
						]);
					}

					$tipoMateriaPrima = data_get($dataPlan, "tipo_materia_prima.$idx");
					if (empty($tipoMateriaPrima)) {
						throw ValidationException::withMessages([
							"tipo_materia_prima.$idx" => "El campo tipo de materia prima es obligatorio."
						]);
					}

					$especie = data_get($dataPlan, "especie.$idx");
					if (empty($especie)) {
						throw ValidationException::withMessages([
							"especie.$idx" => "El campo especie es obligatorio."
						]);
					}

					$tipoTransporte = data_get($dataPlan, "tipo_transporte.$idx");
					if (empty($tipoTransporte)) {
						throw ValidationException::withMessages([
							"tipo_transporte.$idx" => "El campo tipo transporte es obligatorio."
						]);
					}

					$patenteRampla = data_get($dataPlan, "patente_rampla.$idx");
					if (empty($patenteRampla)) {
						throw ValidationException::withMessages([
							"patente_rampla.$idx" => "El campo patente de la rampla es obligatorio."
						]);
					}

					$estadoRampla = data_get($dataPlan, "estado_rampla.$idx");
					if (empty($estadoRampla)) {
						throw ValidationException::withMessages([
							"estado_rampla.$idx" => "El campo estado rampla es obligatorio."
						]);
					}

					$patenteCamion = data_get($dataPlan, "patente_camion.$idx");
					if (empty($patenteCamion)) {
						throw ValidationException::withMessages([
							"patente_camion.$idx" => "El campo patente del camión es obligatorio."
						]);
					}

					$conductor = data_get($dataPlan, "conductor.$idx");
					if (empty($conductor)) {
						throw ValidationException::withMessages([
							"conductor.$idx" => "El campo conductor es obligatorio."
						]);
					}

					// EXISTENCIA DE FECHAS DE CABOTAJE Y DATOS REQUERIDOS EN TIPOS DE TRANSPORTES MARITIMOS
					$tipoTransporteId = $dataPlan['tipo_transporte'][$idx] ?? null;
					if (in_array( (int) $tipoTransporteId, [ config('constantes.TIPO_TRANSPORTE_COMBINADO'), config('constantes.TIPO_TRANSPORTE_BARCAZA'), ], true )) {

						if (empty($dataPlan['fecha_embarque'][$idx] ?? null)) {
							throw ValidationException::withMessages([ "fecha_embarque.$idx" => 'La fecha de embarque es obligatoria para este tipo de transporte.' ]);
						}

						if (empty($dataPlan['fecha_arribo_puerto'][$idx] ?? null)) {
							throw ValidationException::withMessages([ "fecha_arribo_puerto.$idx" => 'La fecha de arribo a puerto es obligatoria para este tipo de transporte.' ]);
						}

						// 🔹 VALIDACIÓN DE SECUENCIA DE FECHAS (solo para transporte con cabotaje)
						$fechaRetiro   = $dataSol['fecha_retiro'][$idx] ?? null;
						$planificada   = $dataPlan['fecha_planificada'][$idx] ?? null;
						$embarque      = $dataPlan['fecha_embarque'][$idx] ?? null;
						$arribo        = $dataPlan['fecha_arribo_puerto'][$idx] ?? null;
						$eta           = $dataPlan['eta_calculada_hidden'][$idx] ?? null;

						// Normalizamos a Carbon SOLO si existen (evita errores con null)
						$retiro   = $fechaRetiro ? \Carbon\Carbon::parse($fechaRetiro) : null;
						$plan     = $planificada ? \Carbon\Carbon::parse($planificada) : null;
						$emb      = $embarque ? \Carbon\Carbon::parse($embarque) : null;
						$arr      = $arribo ? \Carbon\Carbon::parse($arribo) : null;
						$etaC     = $eta ? \Carbon\Carbon::parse($eta) : null;

						// fecha_planificada >= fecha_retiro
						if ($retiro && $plan && $plan->lt($retiro)) {
							throw ValidationException::withMessages([
								"fecha_planificada.$idx" => __('validation.custom.secuencia.planificada_menor_retiro')
							]);
						}

						// fecha_embarque >= fecha_planificada
						if ($plan && $emb && $emb->lt($plan)) {
							throw ValidationException::withMessages([
								"fecha_embarque.$idx" => __('validation.custom.secuencia.embarque_menor_planificada')
							]);
						}

						// fecha_arribo >= fecha_embarque
						if ($emb && $arr && $arr->lt($emb)) {
							throw ValidationException::withMessages([
								"fecha_arribo_puerto.$idx" => __('validation.custom.secuencia.arribo_menor_embarque')
							]);
						}

						// eta >= arribo
						if ($arr && $etaC && $etaC->lt($arr)) {
							throw ValidationException::withMessages([
								"eta_calculada_hidden.$idx" => __('validation.custom.secuencia.eta_menor_arribo')
							]);
						}
					}

					if (in_array( (int) $tipoTransporteId, [ config('constantes.TIPO_TRANSPORTE_COMBINADO'), ], true )) {

						if (empty($dataPlan['fecha_rescate_puerto'][$idx] ?? null)) {
							throw ValidationException::withMessages([ "fecha_rescate_puerto.$idx" => 'La fecha de rescate es obligatoria para este tipo de transporte.' ]);
						}

						if (empty($dataPlan['camion_rescate'][$idx] ?? null)) {
							throw ValidationException::withMessages([ "camion_rescate.$idx" => 'El camión de rescate es obligatorio para este tipo de transporte.' ]);
						}

						if (empty($dataPlan['conductor_rescate'][$idx] ?? null)) {
							throw ValidationException::withMessages([ "conductor_rescate.$idx" => 'El conductor de rescate es obligatorio para este tipo de transporte.' ]);
						}
					}
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

            // 2. Crear retiros y planificaciones asociadas
			$planificacionesCreadas = [];
			foreach ($dataSol['fecha_retiro'] as $index => $fecha) {

				switch ($regionOperativa) {
					case config('constantes.REGION_X'):
						$tipoOperacion = config('constantes.TIPO_OPERACION_RETIRO');
						$kilos         = (float) ($dataSol['kilogramos_estimados'][$index] ?? 0);
				        break;

					case config('constantes.REGION_XII'):
						$tipoOperacion = (int) ($dataSol['tipo_operacion_hidden'][$index] ?? 0);
						$kilos         = (float) ($dataSol['kilogramos_estimados_hidden'][$index] ?? 0);
				        break;

					default:
						throw ValidationException::withMessages([
							"$regionOperativa" => "La región operativa no es válida."
						]);
				}

				$retiro = Retiro::create([	'solicitud_id'         => $solicitud->id,
											'fecha_retiro'         => $fecha,
											'tipo_retiro_id'       => $dataSol['tipo_retiro'][$index],

											'kilogramos_estimados' => $kilos,
											'requiere_reposicion'  => (int) $dataSol['requiere_reposicion_hidden'][$index] ? 1 : 0,
											'cantidad_bins'        => $dataSol['cantidad_bins_hidden'][$index] ?? null,		// Nulo cuando no lo requieren.

											'tipo_operacion'       => $tipoOperacion,

											'estado_id'            => config('constantes.ESTADO_RETIRO_PLANIFICADO'),		// ← Se salta los estados Esperando, Comentado, Aceptado
											'activo'               => true,
										]);

				switch ($regionOperativa) {
					case config('constantes.REGION_X'):														// Región X (modelo tradicional)
						$planificacion = Planificacion::create([	'retiro_id'              => $retiro->id, // ← ID recién creado
												'region_operativa_id'    => $regionOperativa,   // 👈 clave
												'tipo_operacion'         => $tipoOperacion,		// Retiro o Reposición (Acá en REGION X siempre han de ser RETIRO)

												'fecha_hora_planificada' => $dataPlan['fecha_planificada'][$index],
												'duracion_viaje'         => $dataPlan['duracion_viaje'][$index],
												'hora_llegada_estimada'  => $dataPlan['hora_llegada_estimada_hidden'][$index],

												'tipo_materia_prima_id'  => $dataPlan['tipo_materia_prima'][$index],
												'especie_id'             => $dataPlan['especie'][$index],
												'tiene_restriccion'      => (int) $dataPlan['tiene_restriccion_hidden'][$index] ? 1 : 0,

												'camion_id'              => $dataPlan['patente_camion'][$index],
												'patente_rampla'         => $dataPlan['patente_rampla'][$index] ?? null,
												'conductor_id'           => $dataPlan['conductor'][$index],

												'motivo_modificacion_id' => config('constantes.CATALOGO_NO_ESPECIFICADO'),		// Se está creando así que aún no hay motivo de modificación
												'estado_id'              => config('constantes.ESTADO_RETIRO_PLANIFICADO'),		// Se salta los estados Esperando, Comentado, Aceptado
												'activo'                 => true,
											]);
						break;

					case config('constantes.REGION_XII'):													// Región XII (Rampla / Cabotaje)
						$esRetiro = (int) ($dataSol['tipo_operacion_hidden'][$index] ?? 0) === config('constantes.TIPO_OPERACION_RETIRO');

						$planificacion = Planificacion::create([
												'retiro_id'              => $retiro->id, // ← ID recién creado
												'region_operativa_id'    => $regionOperativa,   // 👈 clave
												'tipo_operacion'         => $tipoOperacion,		// Retiro o Reposición

												'fecha_hora_planificada' => $dataPlan['fecha_planificada'][$index],
												'duracion_estimada_dias' => data_get($dataPlan, "duracion_estimada_dias.$index"),
												'eta_calculada'          => $esRetiro ? $dataPlan['eta_calculada_hidden'][$index] : null,

												'tipo_materia_prima_id'  => (int) data_get($dataPlan, "tipo_materia_prima.$index"),
												'especie_id'             => (int) data_get($dataPlan, "especie.$index"),
												'tiene_restriccion'      => (int) $dataPlan['tiene_restriccion_hidden'][$index] ? 1 : 0,

												'tipo_transporte_id'     => data_get($dataPlan, "tipo_transporte.$index"),
												'fecha_embarque'         => $dataPlan['fecha_embarque'][$index] ?? null,
												'fecha_arribo_puerto'    => $dataPlan['fecha_arribo_puerto'][$index] ?? null,

												'rampla_id'              => data_get($dataPlan, "patente_rampla.$index"),
												'estado_rampla_id'       => data_get($dataPlan, "estado_rampla.$index"),
												'camion_id'              => (int) data_get($dataPlan, "patente_camion.$index"),
												'conductor_id'           => (int) data_get($dataPlan, "conductor.$index"),

												'fecha_rescate_puerto'   => $dataPlan['fecha_rescate_puerto'][$index] ?? null,
												'camion_rescate_id'      => $dataPlan['camion_rescate'][$index] ?? null,
												'conductor_rescate_id'   => $dataPlan['conductor_rescate'][$index] ?? null,

												'motivo_modificacion_id' => config('constantes.CATALOGO_NO_ESPECIFICADO'),			// Se está creando así que aún no hay motivo de modificación
												'estado_id'              => config('constantes.ESTADO_RETIRO_PLANIFICADO'),			// Se salta los estados Esperando, Comentado, Aceptado
												'activo'                 => true,
											]);
						break;
				}

				$planificacionesCreadas[] = $planificacion;
			}

			DB::commit();

			// 3. 🔔 Notificaciones solo para Región XII
			if ((int)$regionOperativa === config('constantes.REGION_XII')) {
				foreach ($planificacionesCreadas as $planificacion) {
					try {
						// Envío de correo a Roles Internos
						CorreoHelper::enviarCorreoRolesInternosPlanificacionRegionXII($planificacion);

						// Envío de correo a Transportista
						CorreoHelper::enviarCorreoTransportistaPlanificacionRegionXII($planificacion);

					} catch (\Throwable $e) {
						Log::warning('[Correo Planificación XII] Error envío correo', [
							'planificacion_id' => $planificacion->id,
							'error' => $e->getMessage()
						]);
					}

					try {
						// Envío de mensaje Telegram a Conductor
						$this->notificarConductorTelegramCreacion($planificacion);

					} catch (\Throwable $e) {
						Log::warning('[Telegram XII] Error envío telegram', [
							'planificacion_id' => $planificacion->id,
							'error' => $e->getMessage()
						]);
					}
				}
			}

            // 4. Redirigir con éxito
            return redirect()->route('planificaciones-retiro.index')
                            ->with('status', __('responses.solicitudes.store_manual_success'));

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('❌ Error al grabar nueva solicitud de retiros y sus respectivas planificaciones', [
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withInput()
                        ->with('error', __('responses.solicitudes.store_manual_error'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id)
    {
        $retiro = Retiro::with([
                                'solicitud.maquila.empresa:id,razon_social',
                                'solicitud.maquila.sucursal:id,nombre_sucursal,comuna_id',
                                'solicitud.maquila.sucursal.comuna:id,region_id',
								'solicitud.usuario:id,nombre_usuario,apellidos_usuario,rol_id',
                                'tipoRetiro:id,nombre',
                                'estado:id,nombre',
                                'comentarios.usuario:id,nombre_usuario,apellidos_usuario',
                                'solicitud:id,maquila_id,usuario_id,created_at',
                                'solicitud.usuario.rol:id,nombre',
                                'planificacion' => function ($query) {
                                                                        $query->with([
																			'especie:id,nombre',
                                                                            'tipoMateriaPrima:id,nombre',
                                                                            'estado:id,nombre',
                                                                            'motivoModificacion:id,nombre',

																			'camion:id,patente,empresa_id,tipo_camion_id',
                                                                            'camion.empresa:id,razon_social',
                                                                            'camion.tipoCamion:id,nombre',
                                                                            'conductor:id,nombre,apellido',

																			'rampla:id,patente',
																			'estadoRampla:id,nombre',

																			// 👇 NUEVO – rescate
																			'camionRescate:id,patente',
																			'conductorRescate:id,nombre,apellido',
																		]);
                                }
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

        // Formato para la solicitud y retiro
        $retiro->solicitud->created_at_format = $retiro->solicitud->created_at->format('Y-m-d');
        $retiro->fecha_retiro_format = $retiro->fecha_retiro->format('Y-m-d H:i');

        // Formato para la planificación (si existe)
        if ($retiro->planificacion) {
			$p = $retiro->planificacion;

			switch ( $p->region_operativa_id ) {
				case config('constantes.REGION_X'):
					$p->duracion_horas = $p->duracion_viaje;
					$p->hora_llegada_estimada_format = $p->hora_llegada_estimada->format('Y-m-d H:i');
					break;

				case config('constantes.REGION_XII'):
					$p->duracion_dias = $p->duracion_estimada_dias;
					$p->fecha_hora_llegada_format = $p->eta_calculada->format('Y-m-d H:i');
					break;

				default:
					return null;
			}

			// 👇 Formateamos fechas y forzamos acceso a los nombres (evita que se omitan del JSON)
			$p->fecha_hora_planificada_format = $p->fecha_hora_planificada->format('Y-m-d H:i');

			$p->tipoMateriaPrima?->nombre;
			$p->especie?->nombre;

			$p->camion?->patente;
			$p->camion?->tipoCamion?->nombre;
			$p->camion?->empresa?->razon_social;
			$p->conductor?->nombre_completo;

			$p->tipoTransporte?->nombre;
			$p->rampla?->patente;
			$p->estadoRampla?->nombre;

			$p->fecha_embarque_format = $p->fecha_embarque?->format('Y-m-d H:i');
			$p->fecha_arribo_puerto_format = $p->fecha_arribo_puerto?->format('Y-m-d H:i');
			$p->fecha_rescate_puerto_format = $p->fecha_rescate_puerto?->format('Y-m-d H:i');
			$p->camionRescate?->patente;
			$p->conductorRescate?->nombre_completo;

			$p->estado?->nombre;
			$p->motivoModificacion?->nombre;
        }

        // Formato para los comentarios
        $retiro->comentarios->each(function ($comentario) {
            $comentario->created_at_format = $comentario->created_at->format('Y-m-d H:i');
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
	public function edit(string $Ide)
	{
		$id = Crypt::decrypt($Ide);

		$retiro = Retiro::with([
			'solicitud.maquila.empresa:id,razon_social',
			'solicitud.maquila.sucursal:id,nombre_sucursal,comuna_id',
			'solicitud.maquila.sucursal.comuna:id,region_id',

			'solicitud.usuario:id,nombre_usuario,apellidos_usuario,rol_id',
			'solicitud.usuario.rol:id,nombre',

			'tipoRetiro:id,nombre',
			'estado:id,nombre',

			'comentarios.usuario:id,nombre_usuario,apellidos_usuario',

			'planificacion' => function ($query) {
				$query->with([
					// Núcleo común
					'especie:id,nombre',
					'tipoMateriaPrima:id,nombre',
					'estado:id,nombre',
					'motivoModificacion:id,nombre',

					// Transporte principal
					'camion:id,patente,empresa_id,tipo_camion_id',
					'camion.empresa:id,razon_social',
					'camion.tipoCamion:id,nombre',
					'conductor:id,nombre,apellido',

					// Región XII
					'tipoTransporte:id,nombre',
					'rampla:id,patente',
					'estadoRampla:id,nombre',

					// Rescate
					'camionRescate:id,patente',
					'conductorRescate:id,nombre,apellido',
				]);
			}
		])
		->where('id', $id)
		->firstOrFail();

		return view('planificaciones-retiro.edit', compact('retiro'));
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
		// Log::info('🟦 [STORE SolicitudPlanificacion] Campos hidden recibidos');

		// $fechaRetiro          = $request->input('fecha_retiro', []);
		// $kilosEstimados       = $request->input('kilogramos_estimados', []);
		// $kilosEstimadosHidden = $request->input('kilogramos_estimados_hidden', []);
		// $requiereHidden       = $request->input('requiere_reposicion_hidden', []);
		// $binsHidden           = $request->input('cantidad_bins_hidden', []);
		// $tipoOperacionHidden  = $request->input('tipo_operacion_hidden', []);

		// $fechaPlanificada     = $request->input('fecha_planificada', []);
		// $tieneRestriccion     = $request->input('tiene_restriccion_hidden', []);
		// $fechaEmbarque        = $request->input('fecha_embarque', []);
		// $fechaArriboPuerto    = $request->input('fecha_arribo_puerto', []);
		// $fechaRescatePuerto   = $request->input('fecha_rescate_puerto', []);

		// Log::info('Array de fecha_retiro                 :', $fechaRetiro);
		// Log::info('Array de kilogramos_estimados         :', $kilosEstimados);
		// Log::info('Array de kilogramos_estimados_hidden  :', $kilosEstimadosHidden);
		// Log::info('Array de requiere_reposicion_hidden   :', $requiereHidden);
		// Log::info('Array de cantidad_bins_hidden         :', $binsHidden);
		// Log::info('Array de tipo_operacion_hidden        :', $tipoOperacionHidden);

		// Log::info('Array de fecha_planificada            :', $fechaPlanificada);
		// Log::info('Array de tiene_restriccion_hidden     :', $tieneRestriccion);
		// Log::info('Array de fecha_embarque               :', $fechaEmbarque);
		// Log::info('Array de fecha_arribo_puerto          :', $fechaArriboPuerto);
		// Log::info('Array de fecha_rescate_puerto         :', $fechaRescatePuerto);

		// foreach ($fechaRetiro as $i => $valor) {
		// 	Log::info("🔍 Sol-Retiro[$i] → fecha_retiro                : " . ($fechaRetiro[$i] ?? 'null') );
		// 	Log::info("🔍 Sol-Retiro[$i] → kilogramos_estimados        : " . ($kilosEstimados[$i] ?? 'null') );
		// 	Log::info("🔍 Sol-Retiro[$i] → kilogramos_estimados_hidden : " . ($kilosEstimadosHidden[$i] ?? 'null') );
		// 	Log::info("🔍 Sol-Retiro[$i] → requiere_reposicion_hidden  : " . ($requiereHidden[$i] ?? 'null') );
		// 	Log::info("🔍 Sol-Retiro[$i] → cantidad_bins_hidden        : " . ($binsHidden[$i] ?? 'null') );
		// 	Log::info("🔍 Sol-Retiro[$i] → tipo_operacion_hidden       : " . ($tipoOperacionHidden[$i] ?? 'null') );

		// 	Log::info("🔍 Sol-Planif[$i] → fecha_planificada           : " . ($fechaPlanificada[$i] ?? 'null') );
		// 	Log::info("🔍 Sol-Planif[$i] → tiene_restriccion_hidden    : " . ($tieneRestriccion[$i] ?? 'null') );
		// 	Log::info("🔍 Sol-Planif[$i] → fecha_embarque              : " . ($fechaEmbarque[$i] ?? 'null') );
		// 	Log::info("🔍 Sol-Planif[$i] → fecha_arribo_puerto         : " . ($fechaArriboPuerto[$i] ?? 'null') );
		// 	Log::info("🔍 Sol-Planif[$i] → fecha_rescate_puerto        : " . ($fechaRescatePuerto[$i] ?? 'null') );
		// }

		// ---------------------------------------------------------------------
		// Resolver región operativa en backend (fuente de verdad)
		// Al momento de la creación la región operativa quedo persistida en el mismo registro de la planificaicón
		// ---------------------------------------------------------------------
		$planificacion   = Planificacion::findOrFail($id);
		$planificacion->loadMissing('retiro.solicitud.usuario');	// 👈 Cargamos relación que usaremos más adelante en validación de secuencia de fechas y envío de notificaciones

		$regionOperativa = $planificacion->region_operativa_id;

		// Protección para no procesar una región nueva sin pasar por planificación.
		// Esto evita tener que poner explicitos default en los siguientes switch-case de validacion y persistencia
		// Resguarda corrupción silenciosa de datos, sucursal con región operativa no integrada a la lógica
		if (!in_array($regionOperativa, [ config('constantes.REGION_X'), config('constantes.REGION_XII') ])) {
			throw new \LogicException('Región operativa no soportada');
		}


		// Reglas de nullable/requiered para algunos campos de la planificación
		// Según región operativa y tipo de operacion
		$estadoActual = $planificacion->estado_id;													// Primero determinamos el estado del retiro ...
		$esProgramado = ($estadoActual == config('constantes.ESTADO_RETIRO_PROGRAMADO'));			// ... porque si es PROGRAMADO y corresponde a la Región XII aquellos campos deben ser nullables ...

		$tipoOperacion = $planificacion->tipo_operacion;											// También determinamos el tipo de operación (RETIRO ó REPOSICION)
		$esReposicion = ($tipoOperacion == config('constantes.TIPO_OPERACION_REPOSICION'));			// ... porque si es REPOSICION aquellos campos deben ser nullables ...

		// Entonces si es programado o es reposición los campos deben ser 'nullables'
		// 	- Si es Programado : los campos fechas (Plan, Zarpe y Arribo), duración y tipoTransporte son nullables
		// 	- Si es Reposicion : casi todos los campos deben ser nullables (excepto Fecha Planificaicón)
		$requiredOrNullable = ($esProgramado || $esReposicion) ? 'nullable' : 'required';

		// Otra regla exclusiva para el campo 'eta_calculada'
		$esRetiro = ($tipoOperacion == config('constantes.TIPO_OPERACION_RETIRO'));					// Y si es RETIRO 'eta_calculada' debe ser dataPlan o sino null


		$rulesPlanificacion = [];
		switch ($regionOperativa) {
			case config('constantes.REGION_X'):														// Región X (modelo tradicional)
				$rulesPlanificacion = [
					// Fechas y tiempos
					'fecha_planificada'              => ['required', 'array'],
					'fecha_planificada.0'            => ['required', 'date'],
					'duracion_viaje'                 => ['required', 'array'],
					'duracion_viaje.0'               => ['required', 'regex:/^\d{2}:\d{2}$/'],
					'hora_llegada_estimada_hidden'   => ['required', 'array'],
					'hora_llegada_estimada_hidden.0' => ['required', 'date'],

					// Dominio de negocio
					'tipo_materia_prima'             => ['required', 'array'],
					'tipo_materia_prima.0'           => ['required', 'exists:catalogos,id'],
					'especie'                        => ['required', 'array'],
					'especie.0'                      => ['required', 'exists:catalogos,id'],
					'tiene_restriccion_hidden'       => ['required', 'array'],
					'tiene_restriccion_hidden.0'     => ['required', 'in:0,1'],

					// Transporte
					'patente_camion'                 => ['required', 'array'],
					'patente_camion.0'               => ['required', 'exists:camiones,id'],
					'conductor'                      => ['required', 'array'],
					'conductor.0'                    => ['required', 'exists:conductores,id'],
					'patente_rampla'                 => ['nullable', 'array'],
					'patente_rampla.0'               => ['nullable', 'string', 'max:20'],

					// Auditoría
					'motivo_modificacion_final'      => ['required', 'exists:catalogos,id'],
				];
				break;

			case config('constantes.REGION_XII'):													// Región XII (Rampla / Cabotaje)
				$rulesPlanificacion = [
					// Fechas y tiempos base
					'fecha_planificada'              => ['required', 'array'],
					'fecha_planificada.0'            => ['required', 'date'],

					// Duración y ETA
					'duracion_estimada_dias'         => [$requiredOrNullable, 'array'],
					'duracion_estimada_dias.0'       => [$requiredOrNullable, 'integer', 'min:0'],
					'eta_calculada_hidden'           => [$requiredOrNullable, 'array'],				// Cuando es REPOSICION no es requerida
					'eta_calculada_hidden.0'         => [$requiredOrNullable, 'date'],				// Cuando es REPOSICION no es requerida

					// Dominio de negocio
					'tipo_materia_prima'             => [$requiredOrNullable, 'array'],
					'tipo_materia_prima.0'           => [$requiredOrNullable, 'exists:catalogos,id'],
					'especie'                        => [$requiredOrNullable, 'array'],
					'especie.0'                      => [$requiredOrNullable, 'exists:catalogos,id'],
					'tiene_restriccion_hidden'       => [$requiredOrNullable, 'array'],
					'tiene_restriccion_hidden.0'     => [$requiredOrNullable, 'in:0,1'],

					// Transporte
					'tipo_transporte'                => [$requiredOrNullable, 'array'],
					'tipo_transporte.0'              => [$requiredOrNullable, 'exists:catalogos,id'],
					'fecha_embarque'                 => ['nullable', 'array'],						// A continuación si corrresponde se controla su existencia de forma manual
					'fecha_embarque.0'               => ['nullable', 'date'],						// A continuación si corrresponde se controla su existencia de forma manual
					'fecha_arribo_puerto'            => ['nullable', 'array'],						// A continuación si corrresponde se controla su existencia de forma manual
					'fecha_arribo_puerto.0'          => ['nullable', 'date'],						// A continuación si corrresponde se controla su existencia de forma manual

					// Rampla / Camión / Conductor
					'patente_rampla'                 => [$requiredOrNullable, 'array'],
					'patente_rampla.0'               => [$requiredOrNullable, 'exists:ramplas,id'],
					'estado_rampla'                  => [$requiredOrNullable, 'array'],
					'estado_rampla.0'                => [$requiredOrNullable, 'exists:catalogos,id'],
					'patente_camion'                 => [$requiredOrNullable, 'array'],
					'patente_camion.0'               => [$requiredOrNullable, 'exists:camiones,id'],
					'conductor'                      => [$requiredOrNullable, 'array'],
					'conductor.0'                    => [$requiredOrNullable, 'exists:conductores,id'],

					// Rescate (solo si hay cabotaje)
					'fecha_rescate_puerto'           => ['nullable', 'array'],						// A continuación si corrresponde se controla su existencia de forma manual
					'fecha_rescate_puerto.0'         => ['nullable', 'date'],						// A continuación si corrresponde se controla su existencia de forma manual
					'camion_rescate'                 => ['nullable', 'array'],						// A continuación si corrresponde se controla su existencia de forma manual
					'camion_rescate.0'               => ['nullable', 'exists:camiones,id'],			// A continuación si corrresponde se controla su existencia de forma manual
					'conductor_rescate'              => ['nullable', 'array'],						// A continuación si corrresponde se controla su existencia de forma manual
					'conductor_rescate.0'            => ['nullable', 'exists:conductores,id'],		// A continuación si corrresponde se controla su existencia de forma manual

					// Auditoría
					'motivo_modificacion_final'      => ['required', 'exists:catalogos,id'],
				];
				break;
		}

		// Validación final consolidada
		$data = $request->validate($rulesPlanificacion);

		// Las siguientes validaciones manuales de Región XII se aplican:
		// 	- Si el estado NO es PROGRAMADO, es decir, cuando estuvo en un estado previo (PLANIFICADO)
		//	- Si el tipo_operación ES RETIRO, de lo contrario (REPOSICION) estos campos no vienen con información
		if (!$esProgramado && $esRetiro) {

			// Validación de fecha de zarpado y arribo a puerto si se señalo tipo de transporte que incluye cabotaje
			if ($regionOperativa === config('constantes.REGION_XII')) {
				$tipoTransporteId = (int) ($data['tipo_transporte'][0] ?? null);

				if (in_array( $tipoTransporteId, [ config('constantes.TIPO_TRANSPORTE_COMBINADO'), config('constantes.TIPO_TRANSPORTE_BARCAZA'), ], true)) {

					if (empty($data['fecha_embarque'][0] ?? null)) {
						throw ValidationException::withMessages(['fecha_embarque.0' => 'La fecha de embarque es obligatoria para este tipo de transporte.']);
					}

					if (empty($data['fecha_arribo_puerto'][0] ?? null)) {
						throw ValidationException::withMessages(['fecha_arribo_puerto.0' => 'La fecha de arribo a puerto es obligatoria para este tipo de transporte.']);
					}

					// 🔹 VALIDACIÓN DE SECUENCIA DE FECHAS (solo para transporte con cabotaje)
					if (!$planificacion->retiro) {
						throw new \LogicException('La planificación no tiene retiro asociado.');			// 👉 Fallar explícitamente si no hay retiro
					}

					$fechaRetiro = $planificacion->retiro->fecha_retiro;									// 👉 Fuente de verdad: BD
					$planificada = $data['fecha_planificada'][0] ?? null;
					$embarque    = $data['fecha_embarque'][0] ?? null;
					$arribo      = $data['fecha_arribo_puerto'][0] ?? null;
					$eta         = $data['eta_calculada_hidden'][0] ?? null;

					$retiro = $fechaRetiro ? \Carbon\Carbon::parse($fechaRetiro) : null;
					$plan   = $planificada ? \Carbon\Carbon::parse($planificada) : null;
					$emb    = $embarque ? \Carbon\Carbon::parse($embarque) : null;
					$arr    = $arribo ? \Carbon\Carbon::parse($arribo) : null;
					$etaC   = $eta ? \Carbon\Carbon::parse($eta) : null;

					if ($retiro && $plan && $plan->lt($retiro)) {
						throw ValidationException::withMessages([
							'fecha_planificada.0' => __('validation.custom.secuencia.planificada_menor_retiro')
						]);
					}

					if ($plan && $emb && $emb->lt($plan)) {
						throw ValidationException::withMessages([
							'fecha_embarque.0' => __('validation.custom.secuencia.embarque_menor_planificada')
						]);
					}

					if ($emb && $arr && $arr->lt($emb)) {
						throw ValidationException::withMessages([
							'fecha_arribo_puerto.0' => __('validation.custom.secuencia.arribo_menor_embarque')
						]);
					}

					if ($arr && $etaC && $etaC->lt($arr)) {
						throw ValidationException::withMessages([
							'eta_calculada_hidden.0' => __('validation.custom.secuencia.eta_menor_arribo')
						]);
					}
				}

				if (in_array( $tipoTransporteId, [ config('constantes.TIPO_TRANSPORTE_COMBINADO'), ], true )) {

					if (empty($data['fecha_rescate_puerto'][0] ?? null)) {
						throw ValidationException::withMessages([ "fecha_rescate_puerto.0" => 'La fecha de rescate es obligatoria para este tipo de transporte.' ]);
					}

					if (empty($data['camion_rescate'][0] ?? null)) {
						throw ValidationException::withMessages([ "camion_rescate.0" => 'El camión de rescate es obligatorio para este tipo de transporte.' ]);
					}

					if (empty($data['conductor_rescate'][0] ?? null)) {
						throw ValidationException::withMessages([ "conductor_rescate.0" => 'El conductor de rescate es obligatorio para este tipo de transporte.' ]);
					}
				}
			}

		}

        try {
            $estadoActual = $planificacion->estado_id;
            $nuevo_estado_id = ($estadoActual != config('constantes.ESTADO_RETIRO_PROGRAMADO')) // Si NO esta PROGRAMADA ...
                                    ? config('constantes.ESTADO_RETIRO_PLANIFICADO')          // ... actualizamos o re-actualizamos el estado a PLANIFICADA
                                    : $estadoActual;                                          // Y si es PROGRAMADA se mantiene tal cual

			switch ($regionOperativa) {
				case config('constantes.REGION_X'):														// Región X (modelo tradicional)
					$planificacion->update([	'fecha_hora_planificada' => $data['fecha_planificada'][0],
												'duracion_viaje'         => $data['duracion_viaje'][0],						// ahora viene como string HH:MM directo del input type="time"
												'hora_llegada_estimada'  => $data['hora_llegada_estimada_hidden'][0],		// el campo hidden calculado en JS

												'tipo_materia_prima_id'  => $data['tipo_materia_prima'][0],
												'especie_id'             => $data['especie'][0],
												'tiene_restriccion'      => (int) $data['tiene_restriccion_hidden'][0] ? 1 : 0,

												'camion_id'              => $data['patente_camion'][0],
												'patente_rampla'         => $data['patente_rampla'][0] ?? null,
												'conductor_id'           => $data['conductor'][0],

												'motivo_modificacion_id' => $data['motivo_modificacion_final'],
												'estado_id'              => $nuevo_estado_id, 								// ⚠️ FK -> catalogos.id // Si estaba PROGRAMADA se queda sin cambio.
										]);
					break;

				case config('constantes.REGION_XII'):													// Región XII (Rampla / Cabotaje)
					$planificacion->update([	'fecha_hora_planificada' => $data['fecha_planificada'][0] ?? $planificacion->fecha_hora_planificada,
												'duracion_estimada_dias' => data_get($data, "duracion_estimada_dias.0") ?? $planificacion->duracion_estimada_dias,
												'eta_calculada'          => data_get($data, "eta_calculada_hidden.0") ?? $planificacion->eta_calculada,

												'tipo_materia_prima_id'  => data_get($data, "tipo_materia_prima.0") ?? $planificacion->tipo_materia_prima_id,
												'especie_id'             => data_get($data, "especie.0") ?? $planificacion->especie_id,
												'tiene_restriccion'      => data_get($data, "tiene_restriccion_hidden.0") ?? $planificacion->tiene_restriccion,

												'tipo_transporte_id'     => data_get($data, "tipo_transporte.0") ?? $planificacion->tipo_transporte_id,
												'fecha_embarque'         => data_get($data, "fecha_embarque.0") ?? $planificacion->fecha_embarque,
												'fecha_arribo_puerto'    => data_get($data, "fecha_arribo_puerto.0") ?? $planificacion->fecha_arribo_puerto,

												'rampla_id'              => data_get($data, "patente_rampla.0") ?? $planificacion->rampla_id,
												'estado_rampla_id'       => data_get($data, "estado_rampla.0") ?? $planificacion->estado_rampla_id,
												'camion_id'              => data_get($data, "patente_camion.0") ?? $planificacion->camion_id,
												'conductor_id'           => data_get($data, "conductor.0") ?? $planificacion->conductor_id,

												'fecha_rescate_puerto'   => data_get($data, "fecha_rescate_puerto.0") ?? $planificacion->fecha_rescate_puerto,
												'camion_rescate_id'      => data_get($data, "camion_rescate.0") ?? $planificacion->camion_rescate_id,
												'conductor_rescate_id'   => data_get($data, "conductor_rescate.0") ?? $planificacion->conductor_rescate_id,

												'motivo_modificacion_id' => $data['motivo_modificacion_final'],
												'estado_id'              => $nuevo_estado_id, 								// ⚠️ FK -> catalogos.id // Si estaba PROGRAMADA se queda sin cambio.
										]);
					break;
			}

            $planificacion->sincronizarEstadoRetiro();

            // Precargas de relaciones utiles en el envío de del correo y evitan que el Helper efectúe N+1 querys
            // $planificacion->load([
            //     'retiro.solicitud.usuario', // para tener acceso completo al creador de la solicitud de retiro, destinatario del correo
            // ]);

            // Enviar correo al solicitante
            CorreoHelper::enviarCorreoPlanificacionCreada($planificacion);

			// 🔔 Notificaciones solo para Región XII
			if ((int)$regionOperativa === config('constantes.REGION_XII')) {
				try {
					// Envío de correo a Roles Internos
					CorreoHelper::enviarCorreoRolesInternosPlanificacionRegionXII($planificacion);

					// Envío de correo a Transportista
					CorreoHelper::enviarCorreoTransportistaPlanificacionRegionXII($planificacion);

				} catch (\Throwable $e) {
					Log::warning('[Correo Planificación XII] Error envío correo', [
						'planificacion_id' => $planificacion->id,
						'error' => $e->getMessage()
					]);
				}

				try {
					// Envío de mensaje Telegram a Conductor
					$this->notificarConductorTelegramModificacion($planificacion);

				} catch (\Throwable $e) {
					Log::warning('[Telegram XII] Error envío telegram', [
						'planificacion_id' => $planificacion->id,
						'error' => $e->getMessage()
					]);
				}
			}

            return redirect()
                ->route('planificaciones-retiro.index')
                ->with('status', __('responses.planificacion.update_success'));

        } catch (\Exception $e) {
            Log::error('Error al actualizar planificación', [
                'error' => $e->getMessage(),
                'id'    => $id,
                'user'  => auth()->id ?? null,
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('responses.planificacion.update_failed'));
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
        try {
            $planificacion = Planificacion::findOrFail($id);

            $estadoOriginal = $planificacion->estado_id; // ✅ Capturamos antes de modificar

            $planificacion->activo = false;                                                                 // Marcamos como inactivo el registro de planificación
            if ($estadoOriginal != config('constantes.CATALOGO_NO_ESPECIFICADO')) {                         // Pero solo si no es CRUDO - BLANCO - VACIO - CERO.
                $planificacion->estado_id = config('constantes.ESTADO_RETIRO_CANCELADO');                       // quedará CANCELADO, sino se mantiene CRUDO
            }

            $planificacion->save();
            $planificacion->sincronizarEstadoRetiro($request->comentario_anulacion);

            if ($estadoOriginal == config('constantes.ESTADO_RETIRO_PROGRAMADO')) {                         // ✅ Solo sincroniza con Programas Diarios si estaba en estado PROGRAMADA
                $planificacion->sincronizarEstadosDetallesProgramasDiarios();

                try {                                                                                       // ✅ Y notificac por anulación (cancelación) si estaba en estado PROGRAMADA
                    $this->notificarTransportistaCorreo($planificacion);
                } catch (\Throwable $e) {
                    Log::warning("Error al notificar a transportista: " . $e->getMessage());
                }
                try {
                    $this->notificarConductorTelegramCancelacion($planificacion);
                } catch (\Throwable $e) {
                    Log::warning("Error al notificar a conductor: " . $e->getMessage());
                }
            }

            return redirect()
                    ->back()
                    ->with('status', __('responses.planificacion.delete_success'));

        } catch (\Throwable $e) {
            Log::error('❌ Error al eliminar planificación', [
                'id'    => $id,
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                    ->withInput()
                    ->with('error', __('responses.planificacion.delete_error'));
        }
    }

    /**
     * Notifica al transportista que la planificación programada se anulo (se cancelo).
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    protected function notificarTransportistaCorreo(Planificacion $planificacion): void
    {
        // ✉️ Enviar correo usando método del helper - CorreoHelper
        try {
            // AQUI HELPER DE ENVIO DE CORREO
            CorreoHelper::enviarCorreoPlanificacionCancelada($planificacion);

        } catch (\Throwable $e) {
            Log::warning('No se pudo notificar al transportista por correo', [
                'planificacion_id' => $planificacion->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Notifica al conductor que se le asigno una planificación de la Región XII.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    protected function notificarConductorTelegramCreacion(Planificacion $planificacion): void
    {
        try {
            $conductor = $planificacion->conductor;
            $chatId = $conductor->telegram_chat_id;

            if (empty($chatId)) {
                Log::info('[Telegram] Conductor sin chat_id, se omite envío', [
                                                                                'retiro_id'          => $planificacion->retiro_id,
                                                                                'planificacion_id'   => $planificacion->id,
                                                                                'conductor_id'       => optional($planificacion->conductor)->id,
                                                                            ]);
                return;
            }

            $mensaje = "⛔ Estimado {$conductor->nombre_completo},\n\n"

                . "Se le ha asignado un retiro con el siguiente detalle:\n\n"

                . "🏭 Planta: {$planificacion->retiro->solicitud->maquila->sucursal->nombre_sucursal}\n"
                . "📅 Fecha: " . Carbon::parse($planificacion->fecha_hora_planificada)->format('d/m/Y') . "\n"
                . "⏰ Hora: " . Carbon::parse($planificacion->fecha_hora_planificada)->format('H:i') . "\n"
                . "🚛 Patente Camión: {$planificacion->camion->patente}\n"
                . "🏷️ Tipo Camión: {$planificacion->camion->tipoCamion->nombre}\n"
                . "🔁 Repone Bins: " . ($planificacion->retiro->cantidad_bins > 0 ? $planificacion->retiro->cantidad_bins : 'NO') . "\n\n"

                . "Ante cualquier duda, puede contactar al Coordinador de Retiros de Materia Prima de La Portada.\n\n"

                . "Atentamente,\n"
                . "Sistema de Planificación de Retiro de Materia Prima";

            $enviado =  $conductor->notificarPorTelegram($mensaje, $chatId);

            if (! $enviado) {
                Log::error('[Telegram] Falló envío Telegram', [
                                                                'retiro_id'          => $planificacion->retiro_id,
                                                                'planificacion_id'   => $planificacion->id,
                                                                'conductor_id'       => optional($planificacion->conductor)->id,
                                                            ]);
            }

        } catch (\Throwable $e) {
            Log::warning('No se pudo notificar al conductor por Telegram', [
                'planificacion_id' => $planificacion->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Notifica al conductor que una planificación de la Región XII que tenía asignada sufrió una modificación.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    protected function notificarConductorTelegramModificacion(Planificacion $planificacion): void
    {
        try {
            $conductor = $planificacion->conductor;
            $chatId = $conductor->telegram_chat_id;

            if (empty($chatId)) {
                Log::info('[Telegram] Conductor sin chat_id, se omite envío', [
                                                                                'retiro_id'          => $planificacion->retiro_id,
                                                                                'planificacion_id'   => $planificacion->id,
                                                                                'conductor_id'       => optional($planificacion->conductor)->id,
                                                                            ]);
                return;
            }

            $mensaje = "⛔ Estimado {$conductor->nombre_completo},\n\n"

                . "Se ha asignado o actualizado un retiro con el siguiente detalle::\n\n"

                . "🏭 Planta: {$planificacion->retiro->solicitud->maquila->sucursal->nombre_sucursal}\n"
                . "📅 Fecha: " . Carbon::parse($planificacion->fecha_hora_planificada)->format('d/m/Y') . "\n"
                . "⏰ Hora: " . Carbon::parse($planificacion->fecha_hora_planificada)->format('H:i') . "\n"
                . "🚛 Patente Camión: {$planificacion->camion->patente}\n"
                . "🏷️ Tipo Camión: {$planificacion->camion->tipoCamion->nombre}\n"
                . "🔁 Repone Bins: " . ($planificacion->retiro->cantidad_bins > 0 ? $planificacion->retiro->cantidad_bins : 'NO') . "\n\n"

                . "Ante cualquier duda, puede contactar al Coordinador de Retiros de Materia Prima de La Portada.\n\n"

                . "Atentamente,\n"
                . "Sistema de Planificación de Retiro de Materia Prima";

            $enviado =  $conductor->notificarPorTelegram($mensaje, $chatId);

            if (! $enviado) {
                Log::error('[Telegram] Falló envío Telegram', [
                                                                'retiro_id'          => $planificacion->retiro_id,
                                                                'planificacion_id'   => $planificacion->id,
                                                                'conductor_id'       => optional($planificacion->conductor)->id,
                                                            ]);
            }

        } catch (\Throwable $e) {
            Log::warning('No se pudo notificar al conductor por Telegram', [
                'planificacion_id' => $planificacion->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Notifica al conductor que la planificación programada se anulo (se cancelo).
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    protected function notificarConductorTelegramCancelacion(Planificacion $planificacion): void
    {
        try {
            $conductor = $planificacion->conductor;
            $chatId = $conductor->telegram_chat_id;

            if (empty($chatId)) {
                Log::info('[Telegram] Conductor sin chat_id, se omite envío', [
                                                                                'retiro_id'          => $planificacion->retiro_id,
                                                                                'planificacion_id'   => $planificacion->id,
                                                                                'conductor_id'       => optional($planificacion->conductor)->id,
                                                                            ]);
                return;
            }

            $mensaje = "⛔ Estimado {$conductor->nombre_completo},\n\n"

                . "Le informamos que ha sido cancelada la planificación del siguiente retiro:\n\n"

                . "🏭 Planta: {$planificacion->retiro->solicitud->maquila->sucursal->nombre_sucursal}\n"
                . "📅 Fecha: " . Carbon::parse($planificacion->fecha_hora_planificada)->format('d/m/Y') . "\n"
                . "⏰ Hora: " . Carbon::parse($planificacion->fecha_hora_planificada)->format('H:i') . "\n"
                . "🚛 Patente Camión: {$planificacion->camion->patente}\n"
                . "🏷️ Tipo Camión: {$planificacion->camion->tipoCamion->nombre}\n"
                . "🔁 Repone Bins: " . ($planificacion->retiro->cantidad_bins > 0 ? $planificacion->retiro->cantidad_bins : 'NO') . "\n\n"

                . "Por favor, no proceda con el retiro. Si ya había iniciado el trayecto, contacte inmediatamente al Coordinador.\n\n"

                . "Atentamente,\n"
                . "Sistema de Planificación de Retiro de Materia Prima";

            $enviado =  $conductor->notificarPorTelegram($mensaje, $chatId);

            if (! $enviado) {
                Log::error('[Telegram] Falló envío Telegram', [
                                                                'retiro_id'          => $planificacion->retiro_id,
                                                                'planificacion_id'   => $planificacion->id,
                                                                'conductor_id'       => optional($planificacion->conductor)->id,
                                                            ]);
            }

        } catch (\Throwable $e) {
            Log::warning('No se pudo notificar al conductor por Telegram', [
                'planificacion_id' => $planificacion->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function data(Request $request) {
    }

    /**
     * Ruta intermedia para recibir requierimiento desde correo de notficación de creación de la planificación.
     * Sirve para ocultar TOKEN y no aparezca a mostrar el index.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function verDesdeToken($token)
    {
        try {
            $pivoteCorreo = Crypt::decrypt($token); // es un array: ['campo' => 'retiro_id', 'valor' => 42]
            session()->put('pivoteCorreo', $pivoteCorreo);

            return redirect()->route('planificaciones-retiro.index'); // Redirije al index pero con URL limpia y pivote en sesión
        }
        catch (\Exception $e) { // Token inválido o manipulado
            Log::error('❌ Error al ver desde correo retiro', [
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('planificaciones-retiro.index')->with('error', __('emails.token_invalido_expirado'));
        }
    }

    /**
     * Cierra la planificación y guarda el ticket de cierre.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cerrarPlanificacion(Request $request, $id)
    {
        $request->validate([
            'ticket_cierre' => ['required', 'string', 'max:1000'],
        ]);

        try {
            $planificacion = Planificacion::findOrFail($id);
			$regionOperativa = (int) $planificacion->region_operativa_id;

			// $planificacion->update([
            //     'ticket_cierre'  => $request->ticket_cierre,
            //     'estado_id'      => config('constantes.ESTADO_RETIRO_TERMINADO'), // ⚠️ FK -> catalogos.id
            // ]);

			switch($regionOperativa){
				case config('constantes.REGION_X'):
					$planificacion->update([
						'ticket_cierre'  => $request->ticket_cierre,
						'estado_id'      => config('constantes.ESTADO_RETIRO_TERMINADO'), // ⚠️ FK -> catalogos.id
					]);
					break;

				case config('constantes.REGION_XII'):
					$planificacion->update([
						'ticket_cierre'    => $request->ticket_cierre,
						'estado_id'        => config('constantes.ESTADO_RETIRO_TERMINADO'), 	// ⚠️ FK -> catalogos.id
						'estado_rampla_id' => config('constantes.ENTREGADA_LA_PORTADA'),		// Estado de Rampla -> Entregado en La Portada
					]);
					break;

				default:
					throw new \LogicException(
						"Región operativa no soportada al cerrar planificación (ID {$planificacion->id})"
					);
			}

            $planificacion->sincronizarEstadoRetiro();
            $planificacion->sincronizarEstadosDetallesProgramasDiarios();

            return redirect()
                ->route('planificaciones-retiro.index')
                ->with('status', __('responses.planificacion.cierre_success'));

        } catch (\Throwable $e) {
            Log::error('❌ Error al cerrar planificación y guardar ticker de cierre', [
                'id'    => $id,
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('planificaciones-retiro.index')->with('error', __('responses.planificacion.cierre_error'));
        }
    }
}
