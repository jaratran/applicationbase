<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ValoresGenericosCatalogoSeeder extends Seeder
{
    public function run(): void
    {
        // Paso 1: Insertamos con nombre marcador excéntrico
        DB::table('catalogos')->insert([
            'nombre'       => '***No***especificado***',
            'catalogo_id'  => null,
            'orden'        => 0,
            'activo'       => true,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // Paso 2: Lo actualizamos a ID = 0 y nombre final
        DB::statement("UPDATE catalogos SET id = 0, nombre = 'No especificado' WHERE nombre = '***No***especificado***'");

        echo "✅ Registro insertado como '***No***especificado***' y actualizado a ID = 0 con nombre final.\n";
    }
}
