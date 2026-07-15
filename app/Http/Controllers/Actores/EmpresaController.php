<?php

namespace App\Http\Controllers\Actores;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

use App\Models\Empresa;
use App\Models\Sucursal;
use App\Models\User;

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
                                // ->where('activo', true) -- Se omite este filtro para que salgan todos.
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
    public function store(Request $request)
    {
        try {
		    $request->validate([
		        'tipo_empresa_id'   => 'required|integer|exists:catalogos,id',
		        'rut_empresa'       => 'required|string|max:20',
		        'razon_social'      => 'required|string|max:255',
		        'direccion'         => 'required|string|max:255',
		        'comuna_id'         => 'required|integer|exists:comunas,id',
		        'telefono'          => 'nullable|string|max:30',
		        'email_contacto'    => 'nullable|email|max:255',
		        'telefono_contacto' => 'nullable|string|max:30'
		    ]);

		    Empresa::create($request->all());

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
                                // ->where('activo', true) -- Se omite este filtro para que salgan todos.
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
    public function update(Request $request, $id)
    {
        try {
		    $request->validate([
		        'tipo_empresa_id'   => 'required|integer|exists:catalogos,id',
		        'rut_empresa'       => 'required|string|max:20',
		        'razon_social'      => 'required|string|max:255',
		        'direccion'         => 'required|string|max:255',
		        'comuna_id'         => 'required|integer|exists:comunas,id',
		        'telefono'          => 'nullable|string|max:30',
		        'email_contacto'    => 'nullable|email|max:255',
		        'telefono_contacto' => 'nullable|string|max:30'
		    ]);

		    $empresa = Empresa::findOrFail($id);
		    $empresa->update($request->all());

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

    public function preview(Request $request) {
        //
    }

    public function print($id) {
        //
    }

    /**
     * Entrega las Plantas de Proceso (sucursales) vinculadas a una empresa y cuales está disponibles.
     */
    public function plantas($id)
    {
        // 1. Obtener la empresa con sus plantas asociadas (relación belongsToMany)
        $empresa = Empresa::with('plantasProcesadoras:id,nombre_sucursal')
                        ->select('id', 'rut_empresa', 'razon_social', 'tipo_empresa_id')
                        ->findOrFail($id);

        // 2. Verificar que sea una empresa del tipo Productora de Materias Primas
            // if ($empresa->tipo_empresa_id != config('constantes.TIPO_EMPRESA_PRODUCTORA')) {
            //     abort(403, 'Solo las empresas productoras pueden gestionar vinculación con plantas.');
            // }
        // Cambiamos el abort(403) por un redirect con mensaje de error
        if ($empresa->tipo_empresa_id != config('constantes.TIPO_EMPRESA_PRODUCTORA')) {
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
     * Almacenar vinculos con Plantas de Proceso (sucursales).
     */
    public function guardarPlantas(Request $request, Empresa $empresa)
    {
        try {
		    $request->validate([
		        'sucursales'   => 'array',
		        'sucursales.*' => 'integer|exists:sucursales,id'
		    ]);

		    $empresa->plantasProcesadoras()->sync($request->input('sucursales', []));

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
     * Empresas del tipo PRODUCTORAS.
	 * Compatibles con región operativa del rol del usuario que se Mantiene  (Create/Edit).
	 * Para el tema de la región operativa nos apoyamos con un usuario temporal que preste su accesor.
     */
	public function obtenerEmpresasPorTipoYRol(Request $request, $id)
	{
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

    /**
     * Entrega empresas por tipo para Create/Edit de Solicitudes de Retiro
     * cuando opera un usuario AdminIT o Coordinador.
     */
	public function obtenerEmpresasPorTipo($idTipo)
	{
		$query = Empresa::where('activo', true)
						->where('tipo_empresa_id', $idTipo);

		// 🔒 Solo filtrar por ámbito si es Productora
		if ((int) $idTipo === config('constantes.TIPO_EMPRESA_PRODUCTORA')) {

			$regionesPermitidas = auth()->user()->regiones_operativas_ids;

			$query->whereHas('plantasProcesadoras.comuna', function ($q) use ($regionesPermitidas) {
				$q->whereIn('region_id', $regionesPermitidas);
			});
		}

		return $query
			->orderBy('razon_social')
			->get(['id', 'razon_social']);
	}

    /**
     * Entrega plantas vinculadas en maquila con una productora especifica.
     * Esto permite rellenar los select2 en Create/Edit de Solicitudes de Retiro.
     */
	public function plantasVinculadas($id)
	{
		$regionesPermitidas = auth()->user()->regiones_operativas_ids;

		$plantas = Empresa::findOrFail($id)
			->plantasProcesadoras()
			->with('comuna:id,region_id')
			->whereHas('comuna', function ($q) use ($regionesPermitidas) {
				$q->whereIn('region_id', $regionesPermitidas);
			})
			->orderBy('nombre_sucursal')
			->get()

			->map(function ($sucursal) {
				return [
					'id'                   => $sucursal->id,
					'data'                 => $sucursal->nombre_sucursal,
					'region_operativa_id'  => $sucursal->region_operativa_id,
				];
			});

		return response()->json($plantas);
	}
}
