<?php

namespace App\Http\Controllers\Parametros;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;

use App\Models\DesignParameter;
use App\Models\OperationalParameter;
use App\Models\Catalogo;
use App\Models\CatalogoRelacion;

class ParameterController extends Controller
{
    /**
     * Muestra la vista de parámetros operacionales.
     *
     * @return \Illuminate\View\View
     */

    public function index()
    {
        // Obtenemos el único registro de ambas tablas de parámetros de Diseño y Operacionales
        $designParameter = DesignParameter::first();
        $operationalParameter = OperationalParameter::first();

        // Categorías de Catálogos (solo donde catalogo_id es NULL)
        $categorias = Catalogo::whereNull('catalogo_id')
								->whereNotIn('id', config('constantes.CATALOGOS_NO_FRONT') )                             // Excluir ids de Categorias NO-FRONT específicas
								->orderBy('orden')
								->get();

        // Valores asociados a cada Categoría (donde catalogo_id no es NULL)
        $valoresCatalogo = Catalogo::whereNotNull('catalogo_id')
                                    ->whereNotIn('catalogo_id', config('constantes.CATALOGOS_NO_FRONT') )                 // Excluir valores de Categorias NO-FRONT específicas
                                    ->orderBy('catalogo_id')
                                    ->orderBy('orden')
                                    ->get();

        // Relaciones entre Valores de Categorías del Catalogo
        $relaciones = CatalogoRelacion::with(['origen', 'destino', 'tipo'])->get();

        return view('parameters.index', compact(
            'designParameter',
            'operationalParameter',
            'categorias',
            'valoresCatalogo',
            'relaciones'
        ));
    }

    /**
     * Actualiza los parámetros operacionales.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        try {
            $huboCambios = false; // Variable para verificar si hubo cambios

            // -----------------------------------------------------------------
            // **** *** *** P A R Á M E T R O S   D E   D I S E Ñ O  *** *** ***
            $designParameter = DesignParameter::first();
            if (!$designParameter) {
                $designParameter = DesignParameter::create([
                    'titulo_design' => null,
                    'logo_design' => 'default_logo.png', // o NULL si prefieres
                    'emblema_design' => 'default_emblema.png', // o NULL
                    'favicon_design' => 'default_favicon.ico', // o NULL
                    'fondo_pantalla_design' => 'default_fondo.png', // o NULL
                    'custom_primary' => '#0d6efd',
                    'custom_secondary' => '#6c757d',
                    'custom_success' => '#198754',
                    'custom_warning' => '#ffc107',
                    'custom_danger' => '#dc3545',
                    'custom_info' => '#0dcaf0',
                ]);
            }

            // Validar y actualizar campos de color personalizados
            $request->validate([
                'titulo_design'    => 'required|string|max:255',
                'custom_primary'   => 'required|string|max:255',
                'custom_secondary' => 'required|string|max:255',
                'custom_success'   => 'required|string|max:255',
                'custom_warning'   => 'required|string|max:255',
                'custom_danger'    => 'required|string|max:255',
                'custom_info'      => 'required|string|max:255',

                // Validaciones para archivos (opcional, solo si vienen)
                'fondo_pantalla_design' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
                'logo_design'           => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
                'emblema_design'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
                'favicon_design'        => 'nullable|image|mimes:jpeg,png,jpg,ico,svg,webp|max:1024', // favicon más chico
            ]);

            $designParameter->titulo_design = $request->titulo_design;
            $designParameter->custom_primary = $request->custom_primary;
            $designParameter->custom_secondary = $request->custom_secondary;
            $designParameter->custom_success = $request->custom_success;
            $designParameter->custom_warning = $request->custom_warning;
            $designParameter->custom_danger = $request->custom_danger;
            $designParameter->custom_info = $request->custom_info;

            // Forma más elegante y portable de recalcular el verdadero DOCUMENT_ROOT en entornos locales o personalizados (tipo CPanel).
            $basePublic = realpath(base_path('public')) ?: $_SERVER['DOCUMENT_ROOT'];
            $pathDestino = $basePublic . '/config';

            // FONDO
            if ($request->hasFile('fondo_pantalla_design')) {
                $extension = $request->file('fondo_pantalla_design')->getClientOriginalExtension();
                $fileNameToStore = 'fondo_' . time() . '.' . $extension;

                if ($designParameter->fondo_pantalla_design !== $fileNameToStore) {
                    $request->file('fondo_pantalla_design')->move($pathDestino, $fileNameToStore);

                    if (!empty($designParameter->fondo_pantalla_design) && file_exists($pathDestino . '/' . $designParameter->fondo_pantalla_design)) {
                        unlink($pathDestino . '/' . $designParameter->fondo_pantalla_design);
                    }

                    $designParameter->fondo_pantalla_design = $fileNameToStore;
                }
            }

            // LOGO
            if ($request->hasFile('logo_design')) {
                $extension = $request->file('logo_design')->getClientOriginalExtension();
                $fileNameToStore = 'logo_' . time() . '.' . $extension;

                if ($designParameter->logo_design !== $fileNameToStore) {
                    $request->file('logo_design')->move($pathDestino, $fileNameToStore);

                    if (!empty($designParameter->logo_design) && file_exists($pathDestino . '/' . $designParameter->logo_design)) {
                        unlink($pathDestino . '/' . $designParameter->logo_design);
                    }

                    $designParameter->logo_design = $fileNameToStore;
                }
            }

            // EMBLEMA
            if ($request->hasFile('emblema_design')) {
                $extension = $request->file('emblema_design')->getClientOriginalExtension();
                $fileNameToStore = 'emblema_' . time() . '.' . $extension;

                if ($designParameter->emblema_design !== $fileNameToStore) {
                    $request->file('emblema_design')->move($pathDestino, $fileNameToStore);

                    if (!empty($designParameter->emblema_design) && file_exists($pathDestino . '/' . $designParameter->emblema_design)) {
                        unlink($pathDestino . '/' . $designParameter->emblema_design);
                    }

                    $designParameter->emblema_design = $fileNameToStore;
                }
            }

            // FAVICON
            if ($request->hasFile('favicon_design')) {
                $extension = $request->file('favicon_design')->getClientOriginalExtension();
                $fileNameToStore = 'favicon_' . time() . '.' . $extension;

                if ($designParameter->favicon_design !== $fileNameToStore) {
                    $request->file('favicon_design')->move($pathDestino, $fileNameToStore);

                    if (!empty($designParameter->favicon_design) && file_exists($pathDestino . '/' . $designParameter->favicon_design)) {
                        unlink($pathDestino . '/' . $designParameter->favicon_design);
                    }

                    $designParameter->favicon_design = $fileNameToStore;
                }
            }

            // Se verifica si hubo cambios en los parámetros de diseño o en los archivos de imagenes para guardar y anotar que hubo cambios
            if ($designParameter->isDirty()) {
                $designParameter->save();
                $huboCambios = true;
            }

            // -------------------------------------------------------------------------------
            // **** *** *** P A R Á M E T R O S   D E   O P E R A C I O N A L E S  *** *** ***
            $operationalParameter = OperationalParameter::first();
            if (!$operationalParameter) {
                $operationalParameter = OperationalParameter::create([
                    'support_email' => null,
                    'support_telefono' => null, // Nuevo campo
                    'audit_email' => null,
                    'audit_email_enabled' => 0,
                    'notify_admins_as_coordinators' => 0,
                    'verification_expiration_time' => null, // Nuevo campo
                    'allow_profile_editing' => 1,
                    'average_truck_speed' => 50,
					// Agregados por adicional Solicitudes XII Región
					'maritime_transit_duration_days'    => 3,
					'terrestrial_transit_duration_days' => 3,
					'combined_transit_duration_days' => 3,
					'delay_arribo_eta_hours' => 2,
                ]);
            }

            // Aseguramos que los checkboxes no enviados se interpreten como 'false'
            $request->merge([
                'audit_email_enabled'           => $request->has('audit_email_enabled') ? 1 : 0,
                'notify_admins_as_coordinators' => $request->has('notify_admins_as_coordinators') ? 1 : 0,
                'allow_profile_editing'         => $request->has('allow_profile_editing') ? 1 : 0,
            ]);

            // Validar los datos sin mensajes inline
            $request->validate([
                'support_email'                     => 'nullable|email|max:255',
                'support_telefono'                  => 'nullable|string|max:20', // Nuevo campo
                'audit_email'                       => 'nullable|email|max:255',
                'audit_email_enabled'               => 'nullable|boolean',
                'notify_admins_as_coordinators'     => 'nullable|boolean',
                'verification_expiration_time'      => 'nullable|integer|min:1|max:1440', // 👍 recomendado
                'allow_profile_editing'             => 'nullable|boolean',
                'average_truck_speed'               => 'nullable|integer|min:0',

				// Agregados por adicional Solicitudes XII Región
				'maritime_transit_duration_days'    => 'nullable|integer|min:1|max:30',
				'terrestrial_transit_duration_days' => 'nullable|integer|min:1|max:30',
				'combined_transit_duration_days'    => 'nullable|integer|min:1|max:30',
				'delay_arribo_eta_hours'            => 'nullable|integer|min:1|max:23',
			]);

            // Asignar campos manualmente
            $operationalParameter->support_email                    = $request->input('support_email');
            $operationalParameter->support_telefono                 = $request->input('support_telefono'); // Nuevo campo
            $operationalParameter->audit_email                      = $request->input('audit_email');
            $operationalParameter->audit_email_enabled              = $request->input('audit_email_enabled');
            $operationalParameter->notify_admins_as_coordinators    = $request->input('notify_admins_as_coordinators');
            $operationalParameter->verification_expiration_time     = $request->input('verification_expiration_time'); // Nuevo campo
            $operationalParameter->allow_profile_editing            = $request->input('allow_profile_editing');
            $operationalParameter->average_truck_speed              = $request->input('average_truck_speed');

			// Agregados por adicional Solicitudes XII Región
			$operationalParameter->maritime_transit_duration_days	= $request->input('maritime_transit_duration_days');
			$operationalParameter->terrestrial_transit_duration_days= $request->input('terrestrial_transit_duration_days');
			$operationalParameter->combined_transit_duration_days   = $request->input('combined_transit_duration_days');
			$operationalParameter->delay_arribo_eta_hours           = $request->input('delay_arribo_eta_hours');

            // Verificamos si hubo cambios reales
            if ($operationalParameter->isDirty()) {
                $operationalParameter->save();
                $huboCambios = true;
            }

            // ------------------------------------------------------------------------------------------
            // **** *** *** C A T A L O G O   D E   L I S T A S   C A T E G O R I Z A D A S   *** *** ***
            if ($request->filled('catalogos')) {
                $catalogos = json_decode($request->input('catalogos'), true);

                foreach ($catalogos as $item) {
                    if (!isset($item['nombre']) || !isset($item['catalogo_id'])) {
                        continue; // omitir entradas incompletas
                    }

                    if (isset($item['id']) && $item['id']) {
                        $modelo = Catalogo::find($item['id']);

                        if ($modelo &&
                            ($modelo->nombre !== $item['nombre'] ||
                            (int)$modelo->orden !== (int)$item['orden'] ||
                            (bool)$modelo->activo !== (bool)$item['activo'])) {

                                $modelo->update([
                                    'nombre' => $item['nombre'],
                                    'orden' => $item['orden'] ?? 0,
                                    'activo' => $item['activo'] ?? false,
                                ]);

                                // Si hicimos un update, marcamos que hubo cambios
                                $huboCambios = true;
                        }
                    } else {
                        Catalogo::create([
                            'catalogo_id' => $item['catalogo_id'],
                            'nombre' => $item['nombre'],
                            'orden' => $item['orden'] ?? 0,
                            'activo' => $item['activo'] ?? false,
                        ]);

                        // Si hicimos un create, marcamos que hubo cambios
                        $huboCambios = true;
                    }
                }
            }

            // ----------------------------------------------------------------------------------------------------
            // **** *** *** R E L A C I O N E S   E N T R E   V A L O R E S   D E L   C A T A L O G O   *** *** ***
            if ($request->filled('relaciones')) {
                $relacionesRecibidas = json_decode($request->input('relaciones'), true);

                // Clave para detectar qué relaciones mantener
                $relacionesClave = [];

                foreach ($relacionesRecibidas as $rel) {
                    if ( !isset($rel['valor_origen_id'], $rel['valor_destino_id'], $rel['tipo_relacion_id']) ) {
                        continue;
                    }

                    $clave = $rel['valor_origen_id'] . '-' . $rel['valor_destino_id'] . '-' . $rel['tipo_relacion_id'];
                    $relacionesClave[] = $clave;

                    $existente = CatalogoRelacion::withTrashed()
                        ->where('valor_origen_id', $rel['valor_origen_id'])
                        ->where('valor_destino_id', $rel['valor_destino_id'])
                        ->where('tipo_relacion_id', $rel['tipo_relacion_id'])
                        ->first();

                    if ($existente) {
                        if ($existente->trashed()) {
                            $existente->restore();
                            $huboCambios = true;
                        }
                    } else {
                        CatalogoRelacion::create([
                            'valor_origen_id' => $rel['valor_origen_id'],
                            'valor_destino_id' => $rel['valor_destino_id'],
                            'tipo_relacion_id' => $rel['tipo_relacion_id']
                        ]);
                        $huboCambios = true;
                    }
                }

                // Marcar como eliminadas las que no llegaron en la petición
                $todasRelacionesActuales = CatalogoRelacion::withoutTrashed()->get();

                foreach ($todasRelacionesActuales as $relacion) {
                    $clave = $relacion->valor_origen_id . '-' . $relacion->valor_destino_id . '-' . $relacion->tipo_relacion_id;
                    if (!in_array($clave, $relacionesClave)) {
                        $relacion->delete();
                        $huboCambios = true;
                    }
                }
            }

            if ($huboCambios) {
                return redirect()->back()->with('status', __('responses.parametros.update_success'));
            } else {
                return redirect()->back()->with('status', __('responses.parametros.no_changes_detected'));
            }
        }
        catch (\Throwable $e) {
            Log::error('❌ Error al actualizar parámetros generales', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withInput()->with('error', __('responses.parametros.update_error'));
        }
    }

    /**
     * Retorna en JSON los valores activos de un catálogo (lista)
     * para poblar selectores dinámicos.
     */
    public function listaCatalogo($id)
    {
        $valores = Catalogo::where('catalogo_id', $id)
            ->activos()
            ->orderBy('orden')
            ->get(['id', 'nombre']);

        return response()->json($valores);
    }
}
