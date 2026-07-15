<?php

namespace App\Http\Controllers\Actores;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

use App\Models\Sucursal;
use App\Models\Empresa;
use App\Models\User;
use App\Services\SucursalConfiguration;
use App\Services\MaquilaConfiguration;

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
                            // El estado se muestra y administra desde el mismo listado.
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
    public function store(Request $request, SucursalConfiguration $configuration)
    {
        $data = $configuration->persistableData($request->validate($configuration->rules($request)));

        try {
            Sucursal::create($data);

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
        // Carga también sus empresas asociadas para construir el detalle completo.
        $sucursal = Sucursal::with([    'zona:id,nombre',
                                        'tipoSucursal:id,nombre',
                                        'comuna:id,nombre,region_id',
                                        'comuna.region:id,nombre',
                                        'empresasAtendidas:id,rut_empresa,razon_social,telefono,direccion,comuna_id',
                                        'empresasAtendidas.comuna:id,nombre,region_id',
                                        'empresasAtendidas.comuna.region:id,nombre'
                                    ])
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
    public function update(Request $request, $id, SucursalConfiguration $configuration)
    {
        $data = $configuration->persistableData($request->validate($configuration->rules($request)));

        try {
            $sucursal = Sucursal::findOrFail($id);
            $sucursal->update($data);

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
        $sucursal = Sucursal::findOrFail($id);

        if ($sucursal->activo) {
            $request->validate([
                'observacion_inactividad' => 'required|string|max:500',
            ]);

            $sucursal->observacion_inactividad = $request->input('observacion_inactividad');
        }

        try {
		    $sucursal->activo = !$sucursal->activo;
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

    /**
     * Entrega las empresas productoras vinculadas y disponibles para una planta.
     */
    public function productoras($id, MaquilaConfiguration $configuration)
    {
        // 1. Obtener la sucursal con sus productoras asociadas (relación belongsToMany)
        $sucursal = Sucursal::with('empresasAtendidas:id,razon_social')
                        ->select('id', 'codigo_siep', 'nombre_sucursal', 'tipo_sucursal_id')
                        ->findOrFail($id);

        // La misma restricción se vuelve a comprobar al guardar para proteger el endpoint directo.
        if (!$configuration->canAssociateCompanies($sucursal)) {
            return redirect()->route('sucursal.index')->with('error', __('auth.only_plant_branches_can_link_producers'));
        }

        // 3. Obtener todas las empresas disponibles pero del tipo Productoras de Materias Primas
        $empresas = Empresa::select('id', 'razon_social')
                                ->where('tipo_empresa_id', config('constantes.TIPO_EMPRESA_PRODUCTORA'))
                                ->where('activo', true)
                                ->orderBy('razon_social')->get();

        // 4. Devolver la data para ser usada en el template Handlebars
        return response()->json([
            'sucursal'     => $sucursal,
            'empresas'  => $empresas,
            'asociadas'   => $sucursal->empresasAtendidas->pluck('id')->toArray()
        ]);
    }

    /**
     * Almacenar vínculos con empresas productoras.
     */
    public function guardarProductoras(Request $request, Sucursal $sucursal, MaquilaConfiguration $configuration)
    {
		abort_unless($configuration->canAssociateCompanies($sucursal), 403);
		$data = $request->validate($configuration->companyRules());

		try {
		    // Las asociaciones inactivas no aparecen en el formulario, pero tampoco deben perderse al guardar.
		    $inactiveCompanyIds = $sucursal->empresasVinculadas()
		        ->where(function ($query) {
		            $query->where('empresas.activo', false)
		                ->orWhere('maquilas.activo', false);
		        })
		        ->pluck('empresas.id')
		        ->all();
		    $companyIds = $configuration->preserveInactiveAssociations(
		        $data['empresas'] ?? [],
		        $inactiveCompanyIds
		    );
		    $sucursal->empresasVinculadas()->sync($companyIds);

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
     * Sucursales activas del tipo solicitado compatibles con las regiones del rol.
     */
	public function obtenerSucursalesPorTipoYRol(Request $request, $id, SucursalConfiguration $configuration)
	{
		abort_unless($configuration->isBranchTypeId((int) $id), 404);

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

}
