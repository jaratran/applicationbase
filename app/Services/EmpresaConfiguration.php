<?php

namespace App\Services;

use App\Models\Catalogo;
use App\Models\Empresa;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;

class EmpresaConfiguration
{
    public function rules(): array
    {
        return [
            'tipo_empresa_id' => [
                'required',
                'integer',
                Rule::exists('catalogos', 'id')->where(
                    fn (Builder $query) => $query
                        ->where('catalogo_id', config('constantes.CATEGORIA_TIPO_EMPRESA'))
                        ->where('activo', true)
                        ->whereNull('deleted_at')
                ),
            ],
            'rut_empresa' => ['required', 'string', 'max:20'],
            'razon_social' => ['required', 'string', 'max:255'],
            'direccion' => ['required', 'string', 'max:255'],
            'comuna_id' => ['required', 'integer', 'exists:comunas,id'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'email_contacto' => ['nullable', 'email', 'max:255'],
            'telefono_contacto' => ['nullable', 'string', 'max:30'],
        ];
    }

    public function plantRules(): array
    {
        return [
            'sucursales' => ['array'],
            'sucursales.*' => [
                'integer',
                Rule::exists('sucursales', 'id')->where(
                    fn (Builder $query) => $query
                        ->where('tipo_sucursal_id', config('constantes.TIPO_SUCURSAL_PLANTA'))
                        ->where('activo', true)
                ),
            ],
        ];
    }

    public function canLinkPlants(Empresa $empresa): bool
    {
        return (int) $empresa->tipo_empresa_id === (int) config('constantes.TIPO_EMPRESA_PRODUCTORA');
    }

    public function isCompanyTypeId(int $catalogoId): bool
    {
        return Catalogo::query()
            ->whereKey($catalogoId)
            ->where('catalogo_id', config('constantes.CATEGORIA_TIPO_EMPRESA'))
            ->where('activo', true)
            ->exists();
    }
}
