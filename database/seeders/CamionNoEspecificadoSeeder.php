<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CamionNoEspecificadoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. INSERTAMOS CON NOMBRE EXCÉNTRICO
        DB::table('camiones')->insert([
            'empresa_id'              => 0,
            'conductor_id'            => 0,
            'tipo_camion_id'          => 0,
            'patente'                 => '*NO****ESPECIFICADO*',
            'patente_rampla'          => null,
            'arrendado'               => false,
            'rendimiento_optimo'      => null,
            'activo'                  => true,
            'observacion_inactividad' => null,
            'created_at'              => now(),
            'updated_at'              => now(),
        ]);

        // 2. ACTUALIZAMOS ID A 0 Y NORMALIZAMOS LA PATENTE
        DB::update("
            UPDATE camiones 
            SET id = 0, patente = 'NO ESPECIFICADO' 
            WHERE patente = '*NO****ESPECIFICADO*'
        ");

        echo "✅ Camión 'NO ESPECIFICADO' con ID = 0 creado correctamente.\n";
    }
}
