<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SolicitudSeeder extends Seeder
{
    public function run(): void
    {
        $maquilas = DB::table('maquilas')->pluck('id')->toArray();
        $now = Carbon::now();

        $registros = [];

        for ($i = 0; $i < 100; $i++) {
            $registros[] = [
                'usuario_id'   => 2,
                'maquila_id'   => collect($maquilas)->random(),
                'created_at'   => $now->copy()->subDays(rand(0, 60)),
                'updated_at'   => $now,
            ];
        }

        DB::table('solicitudes')->insert($registros);

        echo "\n✅ Se insertaron 100 solicitudes asignadas al usuario 2.\n";
    }
}
