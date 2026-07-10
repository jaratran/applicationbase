<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CatalogoRelacion;

class CatalogoRelacionSeeder extends Seeder
{
    public function run()
    {
        $tipoRelacionId = 57; // "Agrupa"

        $relaciones = [
            ['origen' => 56, 'destinos' => [44, 45, 46, 47, 48]], // BIN
            ['origen' => 55, 'destinos' => [49, 50, 51]],         // TK
            ['origen' => 54, 'destinos' => [52]],                 // BT
        ];

        foreach ($relaciones as $relacion) {
            foreach ($relacion['destinos'] as $i => $destinoId) {
                CatalogoRelacion::create([
                    'valor_origen_id'   => $relacion['origen'],
                    'valor_destino_id'  => $destinoId,
                    'tipo_relacion_id'  => $tipoRelacionId,
                ]);
            }
        }
    }
}
