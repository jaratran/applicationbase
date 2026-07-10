<?php

namespace App\Http\Controllers\Actores;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

use App\Models\Rampla;
use App\Models\Catalogo;

class RamplaController extends Controller
{
	/**
	 * Mostrar listado de ramplas.
	 */
	public function index()
	{
		$ramplas = Rampla::with([
				'regionOperativa:id,nombre',
				'tipoRampla:id,nombre',
				'capacidadRampla:id,nombre',
				'estadoRampla:id,nombre',
			])
			->where('id', '!=', 0)
			->get([
				'id',
				'patente',
				'region_operativa_id',
				'tipo_rampla_id',
				'capacidad_rampla_id',
				'estado_rampla_id',
				'activo',
			]);

		return view('actores.rampla.index', [
			'ramplas' => $ramplas,
		]);
	}

	/**
	 * Formulario de creación.
	 */
	public function create()
	{
		return view('actores.rampla.create');
	}

	/**
	 * Almacenar nueva rampla.
	 */
	public function store(Request $request)
	{
		try {
			$request->validate([
				'patente' => [
					'required',
					'string',
					'max:20',
					Rule::unique('ramplas')->where(function ($query) use ($request) {
						return $query->where('region_operativa_id', $request->region_operativa_id);
					}),
				],
				'region_operativa_id' => [
					'required',
					'integer',
					'exists:regiones,id',
				],
				'tipo_rampla_id' => [
					'required',
					'integer',
					'exists:catalogos,id',
				],
				'capacidad_rampla_id' => [
					'required',
					'integer',
					'exists:catalogos,id',
				],
				'estado_rampla_id' => [
					'required',
					'integer',
					'exists:catalogos,id',
				],
			]);

			Rampla::create($request->all());

			return redirect()
				->route('rampla.index')
				->with('status', __('auth.trailer_created_successfully'));

		} catch (\Throwable $e) {
			Log::error('❌ Error al crear rampla', [
				'patente' => $request->patente,
				'error'   => $e->getMessage(),
				'line'    => $e->getLine(),
			]);

			return back()
				->withInput()
				->with('error', __('auth.trailer_creation_error'));
		}
	}

	/**
	 * Mostrar detalle de una rampla (JSON).
	 */
	public function show($id)
	{
		$rampla = Rampla::with([
				'regionOperativa:id,nombre',
				'tipoRampla:id,nombre',
				'capacidadRampla:id,nombre',
				'estadoRampla:id,nombre',
			])
			->findOrFail($id, [
				'id',
				'patente',
				'region_operativa_id',
				'tipo_rampla_id',
				'capacidad_rampla_id',
				'estado_rampla_id',
				'activo',
				'observacion_inactividad',
			]);

		return response()->json($rampla);
	}

	/**
	 * Formulario de edición.
	 */
	public function edit($id)
	{
		$ide = Crypt::decrypt($id);

		$rampla = Rampla::findOrFail($ide);

		return view('actores.rampla.edit', [
			'rampla' => $rampla,
		]);
	}

	/**
	 * Actualizar una rampla.
	 */
	public function update(Request $request, $id)
	{
		try {
			$request->validate([
				'patente' => [
					'required',
					'string',
					'max:20',
					Rule::unique('ramplas')
						->ignore($id)
						->where(function ($query) use ($request) {
							return $query->where('region_operativa_id', $request->region_operativa_id);
						}),
				],
				'region_operativa_id' => [
					'required',
					'integer',
					'exists:regiones,id',
				],
				'tipo_rampla_id' => [
					'required',
					'integer',
					'exists:catalogos,id',
				],
				'capacidad_rampla_id' => [
					'required',
					'integer',
					'exists:catalogos,id',
				],
				'estado_rampla_id' => [
					'required',
					'integer',
					'exists:catalogos,id',
				],
			]);

			$rampla = Rampla::findOrFail($id);
			$rampla->update($request->all());

			return redirect()
				->route('rampla.index')
				->with('status', __('auth.trailer_updated_successfully'));

		} catch (\Throwable $e) {
			Log::error('❌ Error al actualizar rampla', [
				'id'    => $id,
				'error' => $e->getMessage(),
				'line'  => $e->getLine(),
			]);

			return back()
				->withInput()
				->with('error', __('auth.trailer_update_error'));
		}
	}

	/**
	 * Anulación / reactivación lógica.
	 */
	public function destroy(Request $request, $id)
	{
		try {
			$rampla = Rampla::findOrFail($id);

			if ($rampla->activo) {
				$request->validate([
					'observacion_inactividad' => 'required|string|max:500',
				]);

				$rampla->observacion_inactividad = $request->observacion_inactividad;
			}

			$rampla->activo = ! $rampla->activo;
			$rampla->save();

			return redirect()
				->route('rampla.index')
				->with(
					'status',
					__('auth.trailer_status_changed', [
						'status' => $rampla->activo ? 'activada' : 'desactivada',
					])
				);

		} catch (\Throwable $e) {
			Log::error('❌ Error al cambiar estado de rampla', [
				'id'    => $id,
				'error' => $e->getMessage(),
				'line'  => $e->getLine(),
			]);

			return back()->with('error', __('auth.trailer_status_change_error'));
		}
	}

    public function obtenerRamplas()
    {
        return Rampla::where('activo', true)
                    ->where('id', '!=', 0)              // Ignoramos aquel registro id=0
                    ->orderBy("patente", "asc")
                    ->get(['id', 'patente']);
	}

	public function estadosPorTipoTransporte(int $tipoTransporteId)
	{
		// Mapa explícito de reglas de negocio
		$mapaEstados = [
			config('constantes.TIPO_TRANSPORTE_TIERRA') => [
				config('constantes.EN_PLANTA_ORIGEN'),
				config('constantes.EN_TRANSITO_TERRESTRE'),
				config('constantes.ENTREGADA_LA_PORTADA'),
			],

			config('constantes.TIPO_TRANSPORTE_BARCAZA') => [
				config('constantes.EN_PLANTA_ORIGEN'),
				config('constantes.EN_PUERTO'),
				config('constantes.EN_TRANSITO_MARITIMO'),
				config('constantes.ARRIBADA_PUERTO_MONTT'),
				config('constantes.ENTREGADA_LA_PORTADA'),
			],

			config('constantes.TIPO_TRANSPORTE_COMBINADO') => [
				config('constantes.EN_PLANTA_ORIGEN'),
				config('constantes.EN_PUERTO'),
				config('constantes.EN_TRANSITO_MARITIMO'),
				config('constantes.ARRIBADA_PUERTO_MONTT'),
				config('constantes.ASIGNADA_A_CAMIÓN'),
				config('constantes.ENTREGADA_LA_PORTADA'),
			],
		];

		if (!isset($mapaEstados[$tipoTransporteId])) {
			return response()->json([]);
		}

		$estados = Catalogo::whereIn('id', $mapaEstados[$tipoTransporteId])
			->activos()
			->orderBy('orden')
			->get(['id', 'nombre']);

		return response()->json($estados);
	}
}
