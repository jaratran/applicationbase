<?php

namespace App\Services;

use App\Models\Catalogo;
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

    public function isCompanyTypeId(int $catalogoId): bool
    {
        return Catalogo::query()
            ->whereKey($catalogoId)
            ->where('catalogo_id', config('constantes.CATEGORIA_TIPO_EMPRESA'))
            ->where('activo', true)
            ->exists();
    }
}
