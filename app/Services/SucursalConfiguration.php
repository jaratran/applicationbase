<?php

namespace App\Services;

use App\Models\Catalogo;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class SucursalConfiguration
{
    public function persistableData(array $validated): array
    {
        return Arr::only($validated, [
            'zona_id',
            'nombre_sucursal',
            'codigo_siep',
            'tipo_sucursal_id',
            'comuna_id',
            'telefono',
            'email',
            'km',
            'tiempo_estimado_viaje',
        ]);
    }

    public function rules(Request $request): array
    {
        return [
            'zona_id' => $this->catalogRule(config('constantes.CATEGORIA_ZONA_SUCURSAL')),
            'nombre_sucursal' => ['required', 'string', 'max:255'],
            'codigo_siep' => ['nullable', 'integer'],
            'tipo_sucursal_id' => $this->catalogRule(config('constantes.CATEGORIA_TIPO_SUCURSAL')),
            'region_id' => [
                'required',
                'integer',
                Rule::exists('regiones', 'id')->where(
                    fn (Builder $query) => $query->where('operativa', true)
                ),
            ],
            'comuna_id' => [
                'required',
                'integer',
                Rule::exists('comunas', 'id')->where(
                    fn (Builder $query) => $query->where('region_id', $request->integer('region_id'))
                ),
            ],
            'telefono' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'km' => ['nullable', 'integer', 'min:0'],
            'tiempo_estimado_viaje' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
        ];
    }

    public function isBranchTypeId(int $catalogoId): bool
    {
        return Catalogo::query()
            ->whereKey($catalogoId)
            ->where('catalogo_id', config('constantes.CATEGORIA_TIPO_SUCURSAL'))
            ->where('activo', true)
            ->exists();
    }

    private function catalogRule(int $categoryId): array
    {
        return [
            'required',
            'integer',
            Rule::exists('catalogos', 'id')->where(
                fn (Builder $query) => $query
                    ->where('catalogo_id', $categoryId)
                    ->where('activo', true)
                    ->whereNull('deleted_at')
            ),
        ];
    }
}
