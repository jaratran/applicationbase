<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConductorNoEspecificadoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('conductores')->insert([
            'empresa_id'              => 0,
            'rut'                     => '0-0',
            'nombre'                  => '***CONDUCTOR***',
            'apellido'                => '***NO***ESPECIFICADO***',
            'telefono'                => null,
            'activo'                  => true,
            'observacion_inactividad' => null,
            'created_at'              => now(),
            'updated_at'              => now(),
        ]);

        DB::update("UPDATE conductores SET id = 0, nombre = 'CONDUCTOR', apellido = 'NO ESPECIFICADO' WHERE nombre = '***CONDUCTOR***' AND apellido = '***NO***ESPECIFICADO***'");

        echo "✅ Conductor genérico con ID = 0 insertado y normalizado.\n";
    }
}
