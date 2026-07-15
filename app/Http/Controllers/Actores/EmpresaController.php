<?php

namespace App\Http\Controllers\Actores;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

use App\Models\Empresa;
use App\Models\Sucursal;
use App\Models\User;
use App\Services\EmpresaConfiguration;
use App\Services\MaquilaConfiguration;

class EmpresaController extends Controller
{
    /**
     * Mostrar listado de empresas activas.
     */
    public function index()
    {
        $empresas = Empresa::with([
                                    'tipoEmpresa:id,nombre',
                                    'comuna:id,nombre,region_id',
                                    'comuna.region:id,nombre'
                                ])
                                // El estado se muestra y administra desde el mismo listado.
                                ->get([
                                    'activo',
                                    'id',
                                    'tipo_empresa_id',
                                    'rut_empresa',
                                    'razon_social',
                                    'telefono',
                                    'direccion',
                                    'comuna_id'
                                ]);

        return view('actores.empresa.index', ['empresas' => $empresas]);
    }

    /**
     * Formulario para crear nueva empresa.
     */
    public function create()
    {
        return view('actores.empresa.create');
    }

    /**
     * Almacenar nueva empresa.
     */
    public function store(Request $request, EmpresaConfiguration $configuration)
    {
        $data = $request->validate($configuration->rules());

        try {
		    Empresa::create($data);

		    return redirect()->route('empresa.index')->with('status', __('auth.company_created_successfully'));

		} catch (\Throwable $e) {
		    Log::error('❌ Error al crear empresa', [
		        'rut'   => $request->rut_empresa,
		        'error' => $e->getMessage(),
		        'line'  => $e->getLine(),
		        'trace' => $e->getTraceAsString(),
		    ]);

		    return back()->withInput()->with('error', __('auth.company_creation_error'));
		}
    }

    /**
     * Retorna la empresa solicitada en formato JSON.
     */
    public function show($id)
    {
        // Obtener la empresa y si tiene rescata sus plantas asociadas (relación belongsToMany)
        $empresa = Empresa::with([  'tipoEmpresa:id,nombre',
                                    'comuna:id,nombre,region_id',
                                    'comuna.region:id,nombre',
                                    'plantasProcesadoras:id,nombre_sucursal,codigo_siep,telefono,comuna_id',
                                    'plantasProcesadoras.comuna:id,nombre,region_id',
                                    'plantasProcesadoras.comuna.region:id,nombre'
                                ])
                                ->findOrFail($id, [
                                    'id',
                                    'activo',
                                    'tipo_empresa_id',
                                    'rut_empresa',
                                    'razon_social',
                                    'direccion',
                                    'comuna_id',
                                    'telefono',
                                    'email_contacto',
                                    'telefono_contacto',
                                    'observacion_inactividad'
                                ]);

        return response()->json($empresa);
    }

    /**
     * Formulario de edición.
     */
    public function edit($id)
    {
        $ide = Crypt::decrypt($id);
        $empresa = Empresa::findOrFail($ide);

        return view('actores.empresa.edit', ["empresa" => $empresa]);
    }

    /**
     * Actualiza la información de una empresa.
     */
    public function update(Request $request, $id, EmpresaConfiguration $configuration)
    {
        $data = $request->validate($configuration->rules());

        try {
		    $empresa = Empresa::findOrFail($id);
		    $empresa->update($data);

		    return redirect()->route('empresa.index')->with('status', __('auth.company_updated_successfully'));

		} catch (\Throwable $e) {
		    Log::error('❌ Error al actualizar empresa', [
		        'id'    => $id,
		        'error' => $e->getMessage(),
		        'line'  => $e->getLine(),
		        'trace' => $e->getTraceAsString(),
		    ]);

		    return back()->withInput()->with('error', __('auth.company_update_error'));
		}
    }

    /**
     * Marca como inactiva una empresa (soft-delete).
     */
    public function destroy(Request $request, $id)
    {
        try {
		    $empresa = Empresa::findOrFail($id);

		    if($empresa->activo){ // Si empresa está activa quiere decir que estamos desactivando y requerimos observación_inactividad
		        $request->validate([
		            'observacion_inactividad' => 'required|string|max:500',
		        ]);

		        $empresa->observacion_inactividad = $request->input('observacion_inactividad');
		    }

		    $empresa->activo = !($empresa->activo);
		    $empresa->save();

		    return redirect()->route('empresa.index')->with('status', __('auth.company_status_changed', ['status' => $empresa->activo ? 'activada' : 'desactivada']));

		} catch (\Throwable $e) {
		    Log::error('❌ Error al cambiar estado de empresa', [
		        'id'    => $id,
		        'error' => $e->getMessage(),
		        'line'  => $e->getLine(),
		        'trace' => $e->getTraceAsString(),
		    ]);

		    return back()->with('error', __('auth.company_status_change_error'));
		}
    }

    /**
     * Entrega las plantas vinculadas y disponibles para una empresa productora.
     */
    public function plantas($id, MaquilaConfiguration $configuration)
    {
        // 1. Obtener la empresa con sus plantas asociadas (relación belongsToMany)
        $empresa = Empresa::with('plantasProcesadoras:id,nombre_sucursal')
                        ->select('id', 'rut_empresa', 'razon_social', 'tipo_empresa_id')
                        ->findOrFail($id);

        // La misma restricción se vuelve a comprobar al guardar para proteger el endpoint directo.
        if (!$configuration->canAssociateBranches($empresa)) {
            return redirect()->route('empresa.index')->with('error', __('auth.only_producer_companies_can_link_plants'));
        }

        // 3. Obtener todas las sucursales disponibles pero del tipo Plantas de Proceso
        $sucursales = Sucursal::select('id', 'nombre_sucursal')
                                ->where('tipo_sucursal_id', config('constantes.TIPO_SUCURSAL_PLANTA')) // Sólo sucursales Plantas de Proceso
                                ->orderBy('nombre_sucursal')->get();

        // 4. Devolver la data para ser usada en el template Handlebars
        return response()->json([
            'empresa'     => $empresa,
            'sucursales'  => $sucursales,
            'asociadas'   => $empresa->plantasProcesadoras->pluck('id')->toArray()
        ]);
    }

    /**
     * Almacenar vínculos con plantas de proceso.
     */
    public function guardarPlantas(Request $request, Empresa $empresa, MaquilaConfiguration $configuration)
    {
        abort_unless($configuration->canAssociateBranches($empresa), 403);
        $data = $request->validate($configuration->branchRules());

        try {
		    // Las asociaciones inactivas no aparecen en el formulario, pero deben conservarse al guardar.
		    $inactiveBranchIds = $empresa->sucursalesVinculadas()
		        ->where(function ($query) {
		            $query->where('sucursales.activo', false)
		                ->orWhere('maquilas.activo', false);
		        })
		        ->pluck('sucursales.id')
		        ->all();
		    $branchIds = $configuration->preserveInactiveAssociations(
		        $data['sucursales'] ?? [],
		        $inactiveBranchIds
		    );
		    $empresa->sucursalesVinculadas()->sync($branchIds);

		    return redirect()->route('empresa.index')->with('status', __('auth.company_plants_linked_successfully'));

		} catch (\Throwable $e) {
		    Log::error('❌ Error al vincular plantas a empresa', [
		        'empresa_id' => $empresa->id,
		        'error'      => $e->getMessage(),
		        'line'       => $e->getLine(),
		        'trace'      => $e->getTraceAsString(),
		    ]);

		    return back()->with('error', __('auth.company_plants_link_error'));
		}
    }

    /**
     * Empresas activas del tipo solicitado compatibles con las regiones del rol.
     */
	public function obtenerEmpresasPorTipoYRol(Request $request, $id, EmpresaConfiguration $configuration)
	{
		abort_unless($configuration->isCompanyTypeId((int) $id), 404);

		$request->validate([
			'rol_id' => 'required|integer',
		]);

		$usuarioTmp = new User([
			'rol_id' => $request->rol_id,
		]);

		$regionesPermitidas = $usuarioTmp->regiones_operativas_ids;

		return Empresa::with('comuna:id,region_id')
			->where('activo', true)
			->where('tipo_empresa_id', $id)
			->whereHas('comuna', function ($q) use ($regionesPermitidas) {
				$q->whereIn('region_id', $regionesPermitidas);
			})
			->orderBy('razon_social')
			->get(['id', 'razon_social']);
	}

}
