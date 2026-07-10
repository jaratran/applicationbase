<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionNoEspecificadaSeeder extends Seeder
{
    public function run(): void
    {
        // Inserta con nombre marcador
        DB::table('regiones')->insert([
            'nombre'     => '***NO***ESPECIFICADO***',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update para dejar con ID = 0 y nombre final
        DB::update("UPDATE regiones SET id = 0, nombre = 'No especificado' WHERE nombre = '***NO***ESPECIFICADO***'");

        $this->command->info('✅ Región "No especificado" insertada y actualizada con ID = 0 correctamente.');
    }
}
