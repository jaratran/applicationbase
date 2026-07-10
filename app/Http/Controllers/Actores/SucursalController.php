<?php

namespace App\Http\Controllers\Actores;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

use App\Models\Sucursal;
use App\Models\Empresa;
use App\Models\User;

class SucursalController extends Controller
{
    /**
     * Mostrar listado de sucursales activas.
     */
    public function index()
    {
        $sucursales = Sucursal::with([
                                'zona:id,nombre',
                                'tipoSucursal:id,nombre',
                                'comuna:id,nombre,region_id',
                                'comuna.region:id,nombre'
                            ])
                            // ->where('activo', true) -- Se omite este filtro para que salgan todos.
                            ->get([
                                'activo',
                                'id',
                                'tipo_sucursal_id',
                                'zona_id',
                                'nombre_sucursal',
                                'codigo_siep',
                                'email',
                                'telefono',
                                'comuna_id'
                            ]);

        return view('actores.sucursal.index', ['sucursales' => $sucursales]);
    }

    /**
     * Formulario para crear nueva sucursal.
     */
    public function create()
    {
        return view('actores.sucursal.create');
    }

    /**
     * Almacenar nueva sucursal.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'zona_id'              => 'required|integer|exists:catalogos,id',
                'nombre_sucursal'      => 'required|string|max:255',
                'codigo_siep'          => 'nullable|integer',
                'tipo_sucursal_id'     => 'required|integer|exists:catalogos,id',
                'comuna_id'            => 'required|integer|exists:comunas,id',
                'telefono'             => 'nullable|string|max:30',
                'email'                => 'nullable|email|max:255',
                'km'                   => 'nullable|integer',
                'tiempo_estimado_viaje'=> 'nullable|numeric|min:0|max:999.99'
            ]);

            Sucursal::create($request->all());

            return redirect()->route('sucursal.index')->with('status', __('auth.branch_created_successfully'));

        } catch (\Throwable $e) {
            Log::error('❌ Error al crear sucursal', [
                'nombre' => $request->nombre_sucursal,
                'error'  => $e->getMessage(),
                'line'   => $e->getLine(),
                'trace'  => $e->getTraceAsString(),
            ]);

            return back()->withInput()->with('error', __('auth.branch_creation_error'));
        }
    }

    /**
     * Retorna la sucursal solicitada en formato JSON.
     */
    public function show($id)
    {
        // Obtener la sucursal y es que tiene sus productoras asociadas (relación belongsToMany)
        $sucursal = Sucursal::with([    'zona:id,nombre',
                                        'tipoSucursal:id,nombre',
                                        'comuna:id,nombre,region_id',
                                        'comuna.region:id,nombre',
                                        'empresasAtendidas:id,rut_empresa,razon_social,telefono,direccion,comuna_id',
                                        'empresasAtendidas.comuna:id,nombre,region_id',
                                        'empresasAtendidas.comuna.region:id,nombre'
                                    ])
                                    // ->where('activo', true) -- Se omite este filtro para que salgan todos.
                                    ->findOrFail($id, [
                                        'id',
                                        'activo',
                                        'zona_id',
                                        'nombre_sucursal',
                                        'codigo_siep',
                                        'tipo_sucursal_id',
                                        'comuna_id',
                                        'telefono',
                                        'email',
                                        'km',
                                        'tiempo_estimado_viaje',
                                        'observacion_inactividad'
                                    ]);

        return response()->json($sucursal);
    }

    /**
     * Formulario de edición.
     */
    public function edit($id)
    {
        $ide = Crypt::decrypt($id);

        $sucursal = Sucursal::findOrFail($ide);

        return view('actores.sucursal.edit', ["sucursal" => $sucursal]);
    }

    /**
     * Actualiza la información de una sucursal.
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'zona_id'              => 'required|integer|exists:catalogos,id',
                'nombre_sucursal'      => 'required|string|max:255',
                'codigo_siep'          => 'nullable|integer',
                'tipo_sucursal_id'     => 'required|integer|exists:catalogos,id',
                'comuna_id'            => 'required|integer|exists:comunas,id',
                'telefono'             => 'nullable|string|max:30',
                'email'                => 'nullable|email|max:255',
                'km'                   => 'nullable|integer',
                'tiempo_estimado_viaje'=> 'nullable|numeric|min:0|max:999.99'
            ]);

            $sucursal = Sucursal::findOrFail($id);
            $sucursal->update($request->all());

            return redirect()->route('sucursal.index')->with('status', __('auth.branch_updated_successfully'));

        } catch (\Throwable $e) {
            Log::error('❌ Error al actualizar sucursal', [
                'id'    => $id,
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withInput()->with('error', __('auth.branch_update_error'));
        }
    }

    /**
     * Marca como inactiva una sucursal (soft-delete).
     */
    public function destroy(Request $request, $id)
    {
        try {
		    $sucursal = Sucursal::findOrFail($id);

		    if($sucursal->activo){ // Si sucursal está activa quiere decir que estamos desactivando y requerimos observación_inactividad
		        $request->validate([
		            'observacion_inactividad' => 'required|string|max:500',
		        ]);

		        $sucursal->observacion_inactividad = $request->input('observacion_inactividad');
		    }

		    $sucursal->activo = !($sucursal->activo);
		    $sucursal->save();

		    return redirect()->route('sucursal.index')->with('status', __('auth.branch_status_changed', ['status' => $sucursal->activo ? 'activada' : 'desactivada']));

		} catch (\Throwable $e) {
		    Log::error('❌ Error al cambiar estado de sucursal', [
		        'id'    => $id,
		        'error' => $e->getMessage(),
		        'line'  => $e->getLine(),
		        'trace' => $e->getTraceAsString(),
		    ]);

		    return back()->with('error', __('auth.branch_status_change_error'));
		}
    }

    public function preview(Request $request) {
        //
    }

    public function print($id) {
        //
    }

    /**
     * Entrega las Productoras de Materia Prima (empresas) vinculadas a una sucursal y cuales está disponibles.
     */
    public function productoras($id)
    {
        // 1. Obtener la sucursal con sus productoras asociadas (relación belongsToMany)
        $sucursal = Sucursal::with('empresasAtendidas:id,razon_social')
                        ->select('id', 'codigo_siep', 'nombre_sucursal', 'tipo_sucursal_id')
                        ->findOrFail($id);

        // 2. Verificar que sea una sucursal del tipo Planta de Proceso
            // if ($sucursal->tipo_sucursal_id != config('constantes.TIPO_SUCURSAL_PLANTA')) {
            //     abort(403, 'Solo las sucursales plantas de poceso pueden gestionar vinculación con plantas.');
            // }
        // Cambiamos el abort(403) por un redirect con mensaje de error
        if ($sucursal->tipo_sucursal_id != config('constantes.TIPO_SUCURSAL_PLANTA')) {
            return redirect()->route('sucursal.index')->with('error', __('auth.only_plant_branches_can_link_producers'));
        }

        // 3. Obtener todas las empresas disponibles pero del tipo Productoras de Materias Primas
        $empresas = Empresa::select('id', 'razon_social')
                                ->where('tipo_empresa_id', config('constantes.TIPO_EMPRESA_PRODUCTORA')) // Sólo empresas Productoras de Materias Primas
                                ->orderBy('razon_social')->get();

        // 4. Devolver la data para ser usada en el template Handlebars
        return response()->json([
            'sucursal'     => $sucursal,
            'empresas'  => $empresas,
            'asociadas'   => $sucursal->empresasAtendidas->pluck('id')->toArray()
        ]);
    }

    /**
     * Almacenar vinculos con Productoras de Materia Prima (empresas).
     */
    public function guardarProductoras(Request $request, Sucursal $sucursal)
    {
		try {
		    $request->validate([
		        'empresas'   => 'array',
		        'sucursal.*' => 'integer|exists:empresas,id'
		    ]);

		    $sucursal->empresasAtendidas()->sync($request->input('empresas', []));

		    return redirect()->route('sucursal.index')->with('status', __('auth.branch_producers_linked_successfully'));

		} catch (\Throwable $e) {
		    Log::error('❌ Error al vincular productoras a sucursal', [
		        'sucursal_id' => $sucursal->id,
		        'error'       => $e->getMessage(),
		        'line'        => $e->getLine(),
		        'trace'       => $e->getTraceAsString(),
		    ]);

		    return back()->with('error', __('auth.branch_producers_link_error'));
		}
    }

    /**
     * Sucursales del tipo PLANTA.
	 *
	 * Compatibles con región operativa del rol del usuario que se Mantiene  (Create/Edit).
     * Admin-IT y Coordinador (tradicional) definen usuarios para ambas regiones operativas.
	 *
	 * Para el tema de la región operativa nos apoyamos con un usuario temporal que preste su accesor.
     */
	public function obtenerSucursalesPorTipoYRol(Request $request, $id)
	{
		$request->validate([
			'rol_id' => 'required|integer',
		]);

		$usuarioTmp = new User([
			'rol_id' => $request->rol_id,
		]);

		$regionesPermitidas = $usuarioTmp->regiones_operativas_ids;

		return Sucursal::with('comuna:id,region_id')
			->where('activo', true)
			->where('tipo_sucursal_id', $id)
			->whereHas('comuna', function ($q) use ($regionesPermitidas) {
				$q->whereIn('region_id', $regionesPermitidas);
			})
			->orderBy('nombre_sucursal')
			->get()
			->map(function ($sucursal) {
				return [
					'id'                  => $sucursal->id,
					'nombre_sucursal'     => $sucursal->nombre_sucursal,
					'region_operativa_id' => $sucursal->region_operativa_id,
				];
			});
	}

    /**
     * Entrega sucursales por Tipo ahora usado en:
     * Create/Edit de Solicitudes de Retiro, al perfil del usuario de la sesión.
	 * - AdminIT/Coordinador X           : Ambas regiones operativas (X y XII)
	 * - Solicitantes (planta/productor) : Sólo región X.
	 * - Coordinar XII                   : Sólo región XII.
	 *
	*/
    public function obtenerSucursalesPorTipo($id)
    {
		$regionesPermitidas = auth()->user()->regiones_operativas_ids;

		return Sucursal::with('comuna:id,region_id')
			->where('activo', true)
			->where('tipo_sucursal_id', $id)
			->whereHas('comuna', function ($q) use ($regionesPermitidas) {			// Sólo sucursales cuya comuna sea de una región operativa
				$q->whereIn('region_id', $regionesPermitidas);
			})
			->orderBy('nombre_sucursal')

			->get()

			->map(function ($sucursal) {
				return [
					'id'                   => $sucursal->id,
					'nombre_sucursal'      => $sucursal->nombre_sucursal,
					'region_operativa_id'  => $sucursal->region_operativa_id,		// Retornamos la región operativa de la sucursal (que es su región)
				];
			});
	}

    /**
     * Entrega productoras vinculadas en maquila con una planta especifica.
     * Esto permite rellenar los select2 en Create/Edit de Solicitudes de Retiro.
     */
    public function productorasVinculadas($id)
    {
        $productoras = Sucursal::findOrFail($id)
                        ->empresasAtendidas()
                        ->select('empresas.id', 'empresas.razon_social as data')
                        ->orderBy('razon_social')
                        ->get();

        return response()->json($productoras);
    }
}
