<?php

namespace App\Services;

use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;

class LocationConfiguration
{
    public function regionRule(bool $operationalOnly = false): array
    {
        return [
            'required',
            'integer',
            Rule::exists('regiones', 'id')->where(function (Builder $query) use ($operationalOnly): void {
                $query->where('id', '<>', 0);

                if ($operationalOnly) {
                    $query->where('operativa', true);
                }
            }),
        ];
    }

    public function communeRule(int $regionId): array
    {
        return [
            'required',
            'integer',
            Rule::exists('comunas', 'id')->where(
                fn (Builder $query) => $query->where('region_id', $regionId)
            ),
        ];
    }
}
