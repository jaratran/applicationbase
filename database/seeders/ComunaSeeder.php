<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ComunaSeeder extends Seeder
{
    public function run(): void
    {
        if (!DB::table('regiones')->where('id', 0)->exists()) {
            throw new RuntimeException('No existe la región neutra requerida para crear la comuna base.');
        }

        DB::table('comunas')->updateOrInsert(
            ['id' => 0],
            [
                'nombre' => 'No especificado',
                'region_id' => 0,
                'updated_at' => now(),
            ]
        );
    }
}
