<?php

namespace App\Http\Controllers\Parametros;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

use App\Models\DesignParameter;
use App\Models\OperationalParameter;
use App\Models\Catalogo;
use App\Models\CatalogoRelacion;

class ParameterController extends Controller
{
    private const DEFAULT_DESIGN_FILES = [
        'default_logo.png',
        'default_emblema.png',
        'default_favicon.ico',
        'default_fondo.png',
    ];

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

        $tiposRelacion = Catalogo::where('catalogo_id', config('constantes.CATEGORIA_TIPO_RELACION'))
            ->activos()
            ->get();

        // Solo se administran relaciones entre valores visibles en esta capacidad.
        $relaciones = CatalogoRelacion::with(['origen', 'destino', 'tipo'])
            ->whereIn('valor_origen_id', $valoresCatalogo->pluck('id'))
            ->whereIn('valor_destino_id', $valoresCatalogo->pluck('id'))
            ->whereIn('tipo_relacion_id', $tiposRelacion->pluck('id'))
            ->get();

        return view('parameters.index', compact(
            'designParameter',
            'operationalParameter',
            'categorias',
            'valoresCatalogo',
            'tiposRelacion',
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
        $request->validate([
            'titulo_design'    => 'required|string|max:255',
            'custom_primary'   => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'custom_secondary' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'custom_success'   => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'custom_warning'   => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'custom_danger'    => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'custom_info'      => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'fondo_pantalla_design' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'logo_design'           => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'emblema_design'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'favicon_design'        => 'nullable|file|mimes:jpeg,png,jpg,ico,webp|max:1024',
            'support_email'                => 'nullable|email|max:255',
            'support_telefono'             => 'nullable|string|max:20',
            'audit_email'                  => 'nullable|email|max:255',
            'audit_email_enabled'          => 'nullable|boolean',
            'verification_expiration_time' => 'nullable|integer|min:1|max:1440',
            'allow_profile_editing'        => 'nullable|boolean',
        ]);

        try {
            $catalogos = $request->filled('catalogos')
                ? $this->validatedCatalogPayload($request->input('catalogos'))
                : null;
            $relationPayload = $request->filled('relaciones')
                ? $this->validatedRelationPayload($request->input('relaciones'))
                : null;
            $huboCambios = false; // Variable para verificar si hubo cambios

            // -----------------------------------------------------------------
            // **** *** *** P A R Á M E T R O S   D E   D I S E Ñ O  *** *** ***
            $designParameter = DesignParameter::first();
            if (!$designParameter) {
                $designParameter = DesignParameter::create([
                    'titulo_design' => 'ApplicationBase',
                    'logo_design' => 'default_logo.png',
                    'emblema_design' => 'default_emblema.png',
                    'favicon_design' => 'default_favicon.ico',
                    'fondo_pantalla_design' => 'default_fondo.png',
                    'custom_primary' => '#0d6efd',
                    'custom_secondary' => '#6c757d',
                    'custom_success' => '#198754',
                    'custom_warning' => '#ffc107',
                    'custom_danger' => '#dc3545',
                    'custom_info' => '#0dcaf0',
                ]);
            }

            $designParameter->titulo_design = $request->titulo_design;
            $designParameter->custom_primary = $request->custom_primary;
            $designParameter->custom_secondary = $request->custom_secondary;
            $designParameter->custom_success = $request->custom_success;
            $designParameter->custom_warning = $request->custom_warning;
            $designParameter->custom_danger = $request->custom_danger;
            $designParameter->custom_info = $request->custom_info;
            $previousImages = [];

            if ($request->hasFile('fondo_pantalla_design')) {
                $previousImages[] = $designParameter->getRawOriginal('fondo_pantalla_design');
                $designParameter->fondo_pantalla_design = $this->storeDesignImage(
                    $request->file('fondo_pantalla_design'),
                    'fondo'
                );
            }

            if ($request->hasFile('logo_design')) {
                $previousImages[] = $designParameter->getRawOriginal('logo_design');
                $designParameter->logo_design = $this->storeDesignImage(
                    $request->file('logo_design'),
                    'logo'
                );
            }

            if ($request->hasFile('emblema_design')) {
                $previousImages[] = $designParameter->getRawOriginal('emblema_design');
                $designParameter->emblema_design = $this->storeDesignImage(
                    $request->file('emblema_design'),
                    'emblema'
                );
            }

            if ($request->hasFile('favicon_design')) {
                $previousImages[] = $designParameter->getRawOriginal('favicon_design');
                $designParameter->favicon_design = $this->storeDesignImage(
                    $request->file('favicon_design'),
                    'favicon'
                );
            }

            // Se verifica si hubo cambios en los parámetros de diseño o en los archivos de imagenes para guardar y anotar que hubo cambios
            if ($designParameter->isDirty()) {
                $designParameter->save();

                foreach ($previousImages as $previousImage) {
                    $this->deletePreviousDesignImage($previousImage);
                }

                $huboCambios = true;
            }

            // -------------------------------------------------------------------------------
            // **** *** *** P A R Á M E T R O S   D E   O P E R A C I O N A L E S  *** *** ***
            $operationalParameter = OperationalParameter::first();
            if (!$operationalParameter) {
                $operationalParameter = OperationalParameter::create([
                    'support_email' => null,
                    'support_telefono' => null,
                    'audit_email' => null,
                    'audit_email_enabled' => 0,
                    'verification_expiration_time' => 60,
                    'allow_profile_editing' => 1,
                ]);
            }

            // Asignar campos manualmente
            $operationalParameter->support_email                    = $request->input('support_email');
            $operationalParameter->support_telefono                 = $request->input('support_telefono');
            $operationalParameter->audit_email                      = $request->input('audit_email');
            $operationalParameter->audit_email_enabled              = $request->boolean('audit_email_enabled');
            $operationalParameter->verification_expiration_time     = $request->input('verification_expiration_time');
            $operationalParameter->allow_profile_editing            = $request->boolean('allow_profile_editing');

            // Verificamos si hubo cambios reales
            if ($operationalParameter->isDirty()) {
                $operationalParameter->save();
                $huboCambios = true;
            }

            // ------------------------------------------------------------------------------------------
            // **** *** *** C A T A L O G O   D E   L I S T A S   C A T E G O R I Z A D A S   *** *** ***
            if ($catalogos !== null) {
                foreach ($catalogos as $item) {
                    if (isset($item['id']) && $item['id']) {
                        $modelo = Catalogo::find($item['id']);

                        if (
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
            if ($relationPayload !== null) {
                $relacionesRecibidas = $relationPayload['relations'];
                $editableValueIds = $relationPayload['editable_value_ids'];
                $relationTypeIds = $relationPayload['relation_type_ids'];

                // Clave para detectar qué relaciones mantener
                $relacionesClave = [];

                foreach ($relacionesRecibidas as $rel) {
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
                $todasRelacionesActuales = CatalogoRelacion::withoutTrashed()
                    ->whereIn('valor_origen_id', $editableValueIds)
                    ->whereIn('valor_destino_id', $editableValueIds)
                    ->whereIn('tipo_relacion_id', $relationTypeIds)
                    ->get();

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
        catch (ValidationException $e) {
            throw $e;
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

    private function storeDesignImage(UploadedFile $file, string $prefix): string
    {
        $destination = public_path('config');
        $fileName = $prefix . '_' . Str::uuid() . '.' . $file->extension();

        $file->move($destination, $fileName);

        return $fileName;
    }

    private function deletePreviousDesignImage(?string $fileName): void
    {
        if ($fileName
            && basename($fileName) === $fileName
            && !in_array($fileName, self::DEFAULT_DESIGN_FILES, true)) {
            $previousPath = public_path('config/' . $fileName);

            if (is_file($previousPath)) {
                unlink($previousPath);
            }
        }
    }

    private function editableCategoryIds(): array
    {
        return Catalogo::whereNull('catalogo_id')
            ->whereNotIn('id', config('constantes.CATALOGOS_NO_FRONT'))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    private function decodeArrayPayload(string $payload, string $field): array
    {
        $decoded = json_decode($payload, true);

        if (!is_array($decoded) || json_last_error() !== JSON_ERROR_NONE) {
            throw ValidationException::withMessages([
                $field => __('validation.custom.' . $field . '.invalid_payload'),
            ]);
        }

        return $decoded;
    }

    private function validatedCatalogPayload(string $payload): array
    {
        $catalogs = $this->decodeArrayPayload($payload, 'catalogos');
        $editableCategoryIds = $this->editableCategoryIds();

        foreach ($catalogs as $item) {
            $valid = isset($item['nombre'], $item['catalogo_id'], $item['orden'], $item['activo'])
                && is_string($item['nombre'])
                && trim($item['nombre']) !== ''
                && mb_strlen($item['nombre']) <= 255
                && filter_var($item['orden'], FILTER_VALIDATE_INT) !== false
                && in_array($item['activo'], [true, false, 0, 1], true)
                && in_array((int) $item['catalogo_id'], $editableCategoryIds, true);

            if ($valid && isset($item['id']) && $item['id']) {
                $catalog = Catalogo::find($item['id']);
                $valid = $catalog && (int) $catalog->catalogo_id === (int) $item['catalogo_id'];
            }

            if (!$valid) {
                throw ValidationException::withMessages([
                    'catalogos' => __('validation.custom.catalogos.invalid_payload'),
                ]);
            }
        }

        return $catalogs;
    }

    private function validatedRelationPayload(string $payload): array
    {
        $relations = $this->decodeArrayPayload($payload, 'relaciones');
        $editableValueIds = Catalogo::whereIn('catalogo_id', $this->editableCategoryIds())
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
        $relationTypeIds = Catalogo::where('catalogo_id', config('constantes.CATEGORIA_TIPO_RELACION'))
            ->activos()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        foreach ($relations as $relation) {
            if (!isset($relation['valor_origen_id'], $relation['valor_destino_id'], $relation['tipo_relacion_id'])
                || !in_array((int) $relation['valor_origen_id'], $editableValueIds, true)
                || !in_array((int) $relation['valor_destino_id'], $editableValueIds, true)
                || !in_array((int) $relation['tipo_relacion_id'], $relationTypeIds, true)) {
                throw ValidationException::withMessages([
                    'relaciones' => __('validation.custom.relaciones.invalid_payload'),
                ]);
            }
        }

        return [
            'relations' => $relations,
            'editable_value_ids' => $editableValueIds,
            'relation_type_ids' => $relationTypeIds,
        ];
    }
}
