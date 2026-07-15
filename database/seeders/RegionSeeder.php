<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('regiones')->updateOrInsert(
            ['id' => 0],
            [
                'nombre' => 'No especificado',
                'orden' => 0,
                'operativa' => false,
                'updated_at' => now(),
            ]
        );
    }
}
