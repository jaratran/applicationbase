<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComunaNoEspecificadaSeeder extends Seeder
{
    public function run(): void
    {
        // Asegúrate de tener una región con id = 1 (o ajusta si es necesario)
        DB::table('comunas')->insert([
            'region_id'  => 0, // <-- este valor debe existir, ajústalo si no existe
            'nombre'     => '***NO***ESPECIFICADO***',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::update("UPDATE comunas SET id = 0, nombre = 'No especificado' WHERE nombre = '***NO***ESPECIFICADO***'");

        $this->command->info('✅ Comuna "No especificado" insertada y actualizada con ID = 0 correctamente.');
    }
}
