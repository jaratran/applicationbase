<?php

namespace App\Http\Controllers\Actores;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

use App\Models\Conductor;
use App\Models\TelegramLink;

class ConductorController extends Controller
{
	/**
	 * Mostrar listado de conductores activos.
	 */
	public function index()
	{
		$conductores = Conductor::with([
			'empresa:id,razon_social',
			'regionOperativa:id,nombre',
		])
			// ->where('activo', true) -- Se omite este filtro para que salgan todos.
			->where('id', '!=', 0)              // El registro neutro no corresponde a un conductor seleccionable
			->get([
				'activo',
				'id',
				'empresa_id',
		        'region_operativa_id',
				'rut',
				'nombre',
				'apellido',
				'telefono',
				'telegram_chat_id'    // 👈 Este es el nuevo campo para saber si está vinculado a Telegram
			]);

		return view('actores.conductor.index', ['conductores' => $conductores]);
	}

	/**
	 * Formulario para crear nuevo conductor.
	 */
	public function create()
	{
		return view('actores.conductor.create');
	}

	/**
	 * Almacenar nuevo conductor.
	 */
	public function store(Request $request)
	{
		try {
			// $request->validate([
			//     'empresa_id'              => 'required|integer|exists:empresas,id',
			//     'rut'                     => 'required|string|max:20',
			//     'nombre'                  => 'required|string|max:255',
			//     'apellido'                => 'required|string|max:255',
			//     'telefono'                => 'nullable|string|max:30',
			//     'observacion_inactividad' => 'nullable|string'
			// ]);

			$request->validate([
				'empresa_id' => [
					'required',
					'integer',
					'exists:empresas,id',
				],
				'region_operativa_id' => [
					'required',
					'integer',
					'exists:regiones,id',
				],
				'rut' => [
					'required',
					'string',
					'max:20',
					Rule::unique('conductores')->where(function ($query) use ($request) {
						return $query->where('region_operativa_id', $request->region_operativa_id);
					}),
				],
				'nombre' => [
					'required',
					'string',
					'max:255',
				],
				'apellido' => [
					'required',
					'string',
					'max:255',
					Rule::unique('conductores')->where(function ($query) use ($request) {
						return $query
							->where('region_operativa_id', $request->region_operativa_id)
							->where('nombre', $request->nombre);
					}),
				],
				'telefono' => [
					'nullable',
					'string',
					'max:30',
				],
				'observacion_inactividad' => [
					'nullable',
					'string',
				],
			]);

			Conductor::create($request->all());

			return redirect()->route('conductor.index')->with('status', __('auth.driver_created_successfully'));
		} catch (\Throwable $e) {
			Log::error('❌ Error al crear conductor', [
				'rut'   => $request->rut,
				'error' => $e->getMessage(),
				'line'  => $e->getLine(),
				'trace' => $e->getTraceAsString(),
			]);

			return back()->withInput()->with('error', __('auth.driver_creation_error'));
		}
	}

	/**
	 * Retorna el conductor solicitado en formato JSON.
	 */
	public function show($id)
	{
		$conductor = Conductor::with([
			'empresa:id,razon_social',
			'regionOperativa:id,nombre',
		])
			// ->where('activo', true) -- Se omite este filtro para que salgan todos.
			->findOrFail($id, [
				'id',
				'activo',
				'empresa_id',
			    'region_operativa_id',
				'rut',
				'nombre',
				'apellido',
				'telefono',
				'observacion_inactividad'
			]);

		return response()->json($conductor);
	}

	/**
	 * Formulario de edición.
	 */
	public function edit($id)
	{
		$ide = Crypt::decrypt($id);

		$conductor = Conductor::findOrFail($ide);

		return view('actores.conductor.edit', ["conductor" => $conductor]);
	}

	/**
	 * Actualiza la información de un conductor.
	 */
	public function update(Request $request, $id)
	{
		try {
			// $request->validate([
			//     'empresa_id'              => 'required|integer|exists:empresas,id',
			//     'rut'                     => 'required|string|max:20',
			//     'nombre'                  => 'required|string|max:255',
			//     'apellido'                => 'required|string|max:255',
			//     'telefono'                => 'nullable|string|max:30',
			//     'observacion_inactividad' => 'nullable|string'
			// ]);

			$request->validate([
				'empresa_id' => [
					'required',
					'integer',
					'exists:empresas,id',
				],
				'region_operativa_id' => [
					'required',
					'integer',
					'exists:regiones,id',
				],
				'rut' => [
					'required',
					'string',
					'max:20',
					Rule::unique('conductores')
						->ignore($id)
						->where(function ($query) use ($request) {
							return $query->where('region_operativa_id', $request->region_operativa_id);
						}),
				],
				'nombre' => [
					'required',
					'string',
					'max:255',
				],
				'apellido' => [
					'required',
					'string',
					'max:255',
					Rule::unique('conductores')
						->ignore($id)
						->where(function ($query) use ($request) {
							return $query
								->where('region_operativa_id', $request->region_operativa_id)
								->where('nombre', $request->nombre);
						}),
				],
				'telefono' => [
					'nullable',
					'string',
					'max:30',
				],
				'observacion_inactividad' => [
					'nullable',
					'string',
				],
			]);

			$conductor = Conductor::findOrFail($id);
			$conductor->update($request->all());

			return redirect()->route('conductor.index')->with('status', __('auth.driver_updated_successfully'));
		} catch (\Throwable $e) {
			Log::error('❌ Error al actualizar conductor', [
				'id'    => $id,
				'error' => $e->getMessage(),
				'line'  => $e->getLine(),
				'trace' => $e->getTraceAsString(),
			]);

			return back()->withInput()->with('error', __('auth.driver_update_error'));
		}
	}

	/**
	 * Marca como inactivo un conductor (soft-delete).
	 */
	public function destroy(Request $request, $id)
	{
		try {
			$conductor = Conductor::findOrFail($id);

			if ($conductor->activo) { // Si conductor está activo quiere decir que estamos desactivando y requerimos observación_inactividad
				$request->validate([
					'observacion_inactividad' => 'required|string|max:500',
				]);

				$conductor->observacion_inactividad = $request->input('observacion_inactividad');
			}

			$conductor->activo = !($conductor->activo);
			$conductor->save();

			return redirect()->route('conductor.index')->with('status', __('auth.driver_status_changed', ['status' => $conductor->activo ? 'activado' : 'desactivado']));
		} catch (\Throwable $e) {
			Log::error('❌ Error al cambiar estado de conductor', [
				'id'    => $id,
				'error' => $e->getMessage(),
				'line'  => $e->getLine(),
				'trace' => $e->getTraceAsString(),
			]);

			return back()->with('error', __('auth.driver_status_change_error'));
		}
	}

	public function preview(Request $request)
	{
		//
	}

	public function print($id)
	{
		//
	}

	/**
	 * Método para obtener los conductores por empresa:
	 * Permite rellenar el selector de conductores utilizado por el mantenedor de Camiones.
	 * Laravel incluye automáticamente nombre_completo porque lo tenemos en $appends del modelo Conductor.
	 */
	public function obtenerConductoresPorEmpresa($empresaId)
	{
		return Conductor::where('empresa_id', $empresaId)
			->where('activo', true)
			->orderBy('nombre')
			->orderBy('apellido')
			->get(['id', 'nombre', 'apellido']);
	}

	/**
	 * Método para obtener un PIN único para vincular conductor con Telegram.
	 */
	public function generarPinTelegram(Conductor $conductor)
	{
		// Revocar cualquier vínculo pendiente o activo anterior
		TelegramLink::where('conductor_id', $conductor->id)
			->whereIn('estado', ['pendiente', 'vinculado'])
			->update(['estado' => 'revocado']);

		// Generar nuevo PIN de 6 dígitos
		$pin = Str::padLeft(strval(random_int(0, 999999)), 6, '0');

		// Crear nuevo registro
		$link = TelegramLink::create([
			'conductor_id'      => $conductor->id,
			'pin'               => $pin,
			'estado'            => 'pendiente',
			'fecha_generacion'  => Carbon::now(),
		]);

		return redirect()->route('conductor.index')->with('status', __('responses.telegram.pin_generado', [
			'pin' => $pin,
			'minutos' => 5
		]));
	}

	/**
	 * Método para desvincular conductor con Telegram. Y no enviarle más mensajes.
	 */
	public function desvincularTelegram(Request $request, Conductor $conductor)
	{
		DB::beginTransaction();
		try {
			// Revocar cualquier vínculo activo
			TelegramLink::where('conductor_id', $conductor->id)
				->where('estado', 'vinculado')
				->update(['estado' => 'revocado']);

			// Primero guardamos el chat_id antes de borrarlo
			$chat_id_original = $conductor->telegram_chat_id;

			// Limpiar campo en tabla conductores
			$conductor->telegram_chat_id = null;
			$conductor->save();

			// Notificar al conductor (si tenía chat_id antes de ser limpiado)
			if (!empty($chat_id_original)) {
				$conductor->notificarPorTelegram(
					__('responses.telegram.desvinculacion_admin', [
						'nombre' => $conductor->nombre . ' ' . $conductor->apellido,
					]),
					$chat_id_original
				);
			}

			DB::commit();

			return redirect()->route('conductor.index')
				->with('status', __('responses.telegram.desvinculado_exito'));
		} catch (\Throwable $e) {
			DB::rollBack();
			Log::error("Error al desvincular Telegram para conductor ID {$conductor->id}: " . $e->getMessage());

			return redirect()->route('conductor.index')
				->with('error', __('responses.telegram.desvinculado_error'));
		}
	}
}
