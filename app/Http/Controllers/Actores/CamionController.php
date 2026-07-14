<?php

namespace App\Http\Controllers\Actores;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

use App\Models\Camion;

class CamionController extends Controller
{
    /**
     * Mostrar listado de camiones activos.
     */
    public function index()
    {
        $camiones = Camion::with([
                                'empresa:id,razon_social',
                                'conductor:id,nombre,apellido',
                                'tipoCamion:id,nombre',
								'regionOperativa:id,nombre'
                            ])
                            //->where('activo', true) -- Se omite este filtro para que salgan todos.
                            ->where('id', '!=', 0)              // El registro neutro no corresponde a un camión seleccionable
                            ->get([
                                'activo',
                                'id',
                                'empresa_id',
                                'conductor_id',
                                'tipo_camion_id',
								'region_operativa_id',
                                'patente',
                                'arrendado',
                                'rendimiento_optimo'
                            ]);

        return view('actores.camion.index', ['camiones' => $camiones]);
    }

    /**
     * Formulario para crear nuevo camión.
     */
    public function create()
    {
        return view('actores.camion.create');
    }

    /**
     * Almacenar nuevo camión.
     */
    public function store(Request $request)
    {
        try {
	        // $request->validate([
	        //     'empresa_id'              => 'required|integer|exists:empresas,id',
	        //     'conductor_id'            => 'required|integer|exists:conductores,id',
	        //     'tipo_camion_id'          => 'required|integer|exists:catalogos,id',
	        //     'patente'                 => 'required|string|max:20',
	        //     'arrendado'               => 'required|boolean',
	        //     'rendimiento_optimo'      => 'nullable|numeric|min:0|max:9999.99',
	        //     'observacion_inactividad' => 'nullable|string'
	        // ]);

			$request->validate([
				'empresa_id' => [
					'required',
					'integer',
					'exists:empresas,id',
				],
				'conductor_id' => [
					'required',
					'integer',
					'exists:conductores,id',
				],
				'tipo_camion_id' => [
					'required',
					'integer',
					'exists:catalogos,id',
				],
				'region_operativa_id' => [
					'required',
					'integer',
					'exists:regiones,id',
				],
				'patente' 				=> [
					'required',
					'string',
					'max:20',
					Rule::unique('camiones')->where(function ($query) use ($request) {
						return $query->where('region_operativa_id', $request->region_operativa_id);
					}),
				],
				'arrendado' 		=> [
					'required',
					'boolean'
				],
				'rendimiento_optimo' 		=> [
					'nullable',
					'numeric',
					'min:0',
					'max:9999.99',
				],
				'observacion_inactividad' 		=> [
					'nullable',
					'string'
				]
			]);

			Camion::create($request->all());

	        return redirect()->route('camion.index')->with('status', __('auth.truck_created_successfully'));

	    } catch (\Throwable $e) {
	        Log::error('❌ Error al crear camión', [
	            'patente' => $request->patente,
	            'error'   => $e->getMessage(),
	            'line'    => $e->getLine(),
	            'trace'   => $e->getTraceAsString(),
	        ]);

	        return back()->withInput()->with('error', __('auth.truck_creation_error'));
	    }
    }

    /**
     * Retorna el camión solicitado en formato JSON.
     */
    public function show($id)
    {
        $camion = Camion::with([
                            'empresa:id,razon_social',
                            'conductor:id,nombre,apellido',
                            'tipoCamion:id,nombre',
							'regionOperativa:id,nombre'
                        ])
                        // ->where('activo', true) -- Se omite este filtro para que salgan todos.
                        ->findOrFail($id, [
                            'id',
                            'activo',
                            'empresa_id',
                            'conductor_id',
                            'tipo_camion_id',
							'region_operativa_id',
                            'patente',
                            'arrendado',
                            'rendimiento_optimo',
                            'observacion_inactividad'
                        ]);

        return response()->json($camion);
    }

    /**
     * Formulario de edición.
     */
    public function edit($id)
    {
        $ide = Crypt::decrypt($id);

        $camion = Camion::findOrFail($ide);

        return view('actores.camion.edit', ["camion" => $camion]);
    }

    /**
     * Actualiza la información de un camión.
     */
    public function update(Request $request, $id)
    {
        try {
	        // $request->validate([
	        //     'empresa_id'              => 'required|integer|exists:empresas,id',
	        //     'conductor_id'            => 'required|integer|exists:conductores,id',
	        //     'tipo_camion_id'          => 'required|integer|exists:catalogos,id',
	        //     'patente'                 => 'required|string|max:20',
	        //     'arrendado'               => 'required|boolean',
	        //     'rendimiento_optimo'      => 'nullable|numeric|min:0|max:9999.99',
	        //     'observacion_inactividad' => 'nullable|string'
	        // ]);


			$request->validate([
				'empresa_id' => [
					'required',
					'integer',
					'exists:empresas,id',
				],
				'conductor_id' => [
					'required',
					'integer',
					'exists:conductores,id',
				],
				'tipo_camion_id' => [
					'required',
					'integer',
					'exists:catalogos,id',
				],
				'region_operativa_id' => [
					'required',
					'integer',
					'exists:regiones,id',
				],
				'patente' 				=> [
					'required',
					'string',
					'max:20',
				],
				'arrendado' 		=> [
					'required',
					'boolean'
				],
				'rendimiento_optimo' 		=> [
					'nullable',
					'numeric',
					'min:0',
					'max:9999.99',
				],
				'observacion_inactividad' 		=> [
					'nullable',
					'string'
				]
			]);

	        $camion = Camion::findOrFail($id);
	        $camion->update($request->all());

	        return redirect()->route('camion.index')->with('status', __('auth.truck_updated_successfully'));

	    } catch (\Throwable $e) {
	        Log::error('❌ Error al actualizar camión', [
	            'id'     => $id,
	            'error'  => $e->getMessage(),
	            'line'   => $e->getLine(),
	            'trace'  => $e->getTraceAsString(),
	        ]);

	        return back()->withInput()->with('error', __('auth.truck_update_error'));
	    }
    }

    /**
     * Marca como inactivo un camión (soft-delete).
     */
    public function destroy(Request $request, $id)
    {
        try {
	        $camion = Camion::findOrFail($id);

	        if($camion->activo){ // Si camion está activo quiere decir que estamos desactivando y requerimos observación_inactividad
	            $request->validate([
	                'observacion_inactividad' => 'required|string|max:500',
	            ]);

	            $camion->observacion_inactividad = $request->input('observacion_inactividad');
	        }

	        $camion->activo = !($camion->activo);
	        $camion->save();

	        return redirect()->route('camion.index')->with('status', __('auth.truck_status_changed', ['status' => $camion->activo ? 'activado' : 'desactivado']));

	    } catch (\Throwable $e) {
	        Log::error('❌ Error al cambiar estado de camión', [
	            'id'     => $id,
	            'error'  => $e->getMessage(),
	            'line'   => $e->getLine(),
	            'trace'  => $e->getTraceAsString(),
	        ]);

	        return back()->with('error', __('auth.truck_status_change_error'));
	    }
    }

    public function preview(Request $request) {
        //
    }

    public function print($id) {
        //
    }

    public function detalleCamion($id)
    {
        return Camion::where('id', $id)
            ->with([
                'empresa:id,razon_social',
                'tipoCamion:id,nombre',
                'conductor:id,nombre,apellido' // No hace falta pedir nombre_completo si el accessor existe
            ])
            ->first(['id', 'empresa_id', 'tipo_camion_id', 'conductor_id']);
    }

    public function obtenerCamiones()
    {
        return Camion::where('activo', true)
                    ->where('id', '!=', 0)              // Ignoramos aquel registro id=0
                    ->orderBy("patente", "asc")
                    ->get(['id', 'patente']);

   }

    public function obtenerCamionesPorEmpresa($id)
    {
        return Camion::where('activo', true)
                    ->where('id', '!=', 0)              // Ignoramos aquel registro id=0
                    ->where('empresa_id', $id)
                    ->orderBy("patente", "asc")
                    ->get(['id', 'patente']);
    }

    public function obtenerCamionesPorTipo($tipoId)
    {
        return Camion::where('activo', true)
                    ->where('id', '!=', 0)              // Ignoramos aquel registro id=0
                    ->where('tipo_camion_id', $tipoId)
                    ->select('id', 'patente')
                    ->orderBy("patente", "asc")
                    ->get(['id', 'patente']);
    }

}
