<?php

namespace App\Services;

use App\Models\Empresa;
use App\Models\Sucursal;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;

class MaquilaConfiguration
{
    public function branchRules(): array
    {
        return [
            'sucursales' => ['array'],
            'sucursales.*' => [
                'integer',
                'distinct',
                Rule::exists('sucursales', 'id')->where(
                    fn (Builder $query) => $query
                        ->where('tipo_sucursal_id', config('constantes.TIPO_SUCURSAL_PLANTA'))
                        ->where('activo', true)
                ),
            ],
        ];
    }

    public function companyRules(): array
    {
        return [
            'empresas' => ['array'],
            'empresas.*' => [
                'integer',
                'distinct',
                Rule::exists('empresas', 'id')->where(
                    fn (Builder $query) => $query
                        ->where('tipo_empresa_id', config('constantes.TIPO_EMPRESA_PRODUCTORA'))
                        ->where('activo', true)
                ),
            ],
        ];
    }

    public function canAssociateBranches(Empresa $empresa): bool
    {
        return (int) $empresa->tipo_empresa_id === (int) config('constantes.TIPO_EMPRESA_PRODUCTORA');
    }

    public function canAssociateCompanies(Sucursal $sucursal): bool
    {
        return (int) $sucursal->tipo_sucursal_id === (int) config('constantes.TIPO_SUCURSAL_PLANTA');
    }

    public function preserveInactiveAssociations(array $requestedIds, array $inactiveIds): array
    {
        return array_values(array_unique(array_merge($requestedIds, $inactiveIds)));
    }
}
