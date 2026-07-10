<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RetirosSeeder extends Seeder
{
    public function run()
    {
        // Tipos de retiro
        $tiposRetiro = [35, 36]; // Tolva, Bins

        // Estados y su distribución aproximada
        $estadosDistribuidos = array_merge(
            array_fill(0, 30, 91), // Esperando
            array_fill(0, 20, 92), // Comentado
            array_fill(0, 10, 93), // Aceptado
            array_fill(0, 20, 94), // Planificado
            array_fill(0, 15, 95), // Terminado
            array_fill(0, 5, 96)   // Cancelado
        );

        $solicitudes = DB::table('solicitudes')->select('id', 'created_at')->get();
        $retirosData = [];

        foreach ($solicitudes as $solicitud) {
            $retirosCount = rand(1, 10);
            for ($i = 0; $i < $retirosCount; $i++) {
                $tipoRetiro = $tiposRetiro[array_rand($tiposRetiro)];
                $requiereReposicion = (bool)rand(0, 1);

                $cantidadBins = null;
                if ($tipoRetiro === 36 && $requiereReposicion) {
                    $cantidadBins = rand(5, 25);
                }

                $retirosData[] = [
                    'solicitud_id'        => $solicitud->id,
                    'fecha_retiro'        => Carbon::parse($solicitud->created_at)->addDays(rand(1, 15))->setTime(rand(8, 18), rand(0, 59)),
                    'tipo_retiro_id'      => $tipoRetiro,
                    'kilogramos_estimados'=> rand(100, 1000),
                    'requiere_reposicion' => $requiereReposicion,
                    'cantidad_bins'       => $cantidadBins,
                    'estado'              => $estadosDistribuidos[array_rand($estadosDistribuidos)],
                    'activo'              => 1,
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ];
            }
        }

        // Insertar en la tabla
        DB::table('retiros')->insert($retirosData);
    }
}
