<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Arr;

class RetirosHistorialSeeder extends Seeder
{
    public function run(): void
    {
        $retiros = DB::table('retiros')->get();
        $total = $retiros->count();
        $cantidadObjetivo = intval($total * 0.8);
        $this->command->info("q retirosAProcesar   : {$cantidadObjetivo}.");

        // Seleccionamos aleatoriamente el 80% de los retiros
        $retirosAProcesar = $retiros->random($cantidadObjetivo);

        // Frases para motivo de cambio
        $motivos = [
            'El sistema tuvo un arrebato y decidió cambiarlo solo.',
            'El jefe dijo que lo hiciera, pero no explicó por qué.',
            'Reajuste de kilogramos por interferencia cósmica.',
            'Cambio validado por la abuela experta en Tolvas.',
            'Se corrigió porque parecía sospechosamente perfecto.',
            'El operador pestañeó y apretó el botón sin querer.',
            'Ajuste por diferencia entre Tolva y Bins… otra vez.',
            'Simulación de terremoto causó variaciones aleatorias.',
            'Cambio por presión popular en la planta.',
            'Modificación motivada por la mística del sistema.',
        ];

        foreach ($retirosAProcesar as $retiro) {
            $cantidadHistorial = rand(1, 9);

            $created = Carbon::parse($retiro->created_at);
            $updated = Carbon::parse($retiro->updated_at);

            // Si no hay diferencia de tiempo, corregimos el retiro mismo
            if ($created->equalTo($updated)) {
                $updated = $created->copy()->addMinutes(rand(30, 120));

                // Aplicamos la corrección en la tabla retiros
                DB::table('retiros')
                    ->where('id', $retiro->id)
                    ->update(['updated_at' => $updated->format('Y-m-d H:i:s')]);
            }

            // Generamos fechas intermedias
            $fechas = collect();
            if ($cantidadHistorial === 1) {
                $fechas->push($updated);
            } else {
                $periodo = $created->diffInMinutes($updated);
                $intervalo = intval($periodo / ($cantidadHistorial - 1));
                for ($i = 0; $i < $cantidadHistorial - 1; $i++) {
                    $fechas->push($updated->copy()->subMinutes($intervalo * $i));
                }
                $fechas->push($updated); // El último igual a updated_at
            }

            // Valores base del retiro
            $base = [
                'fecha_retiro'         => Carbon::parse($retiro->fecha_retiro),
                'tipo_retiro_id'       => $retiro->tipo_retiro_id,
                'kilogramos_estimados' => $retiro->kilogramos_estimados,
                'requiere_reposicion'  => $retiro->requiere_reposicion,
                'cantidad_bins'        => $retiro->cantidad_bins ?? 0,
            ];

            $registro = $base;
            for ($i = 0; $i < $cantidadHistorial; $i++) {

                $huboCambio = false;
                while(! $huboCambio ){
                    
                    if (rand(1, 100) <= 20) { // 20% de probabilidad de cambiar fecha_retiro
                        $registro['fecha_retiro'] = $registro['fecha_retiro']->copy()->addMinutes(rand(-30, 30));
                        $huboCambio = true;
                    }

                    if (rand(1, 100) <= 20) { // 20% de probabilidad de cambiar kilogramos_estimados
                        $registro['kilogramos_estimados'] *= (1 + (rand(-5, 5) / 100)); // +-5%
                        $huboCambio = true;
                    }

                    if (rand(1, 100) <= 20) { // 20% de probabilidad de cambiar tipo_retiro_id
                        $registro['tipo_retiro_id'] = ($registro['tipo_retiro_id'] == 35) ? 36 : 35;
                        $huboCambio = true;
                    }
                    
                    if ($registro['tipo_retiro_id'] == 36) { // SI es BINs ...

                        if (rand(1, 100) <= 20) { // 20% de probabilidad de cambiar requiere_reposicion
                            $registro['requiere_reposicion'] = $registro['requiere_reposicion']? 0: 1; // Invertimos
                            $huboCambio = true;
                        }

                    } else { // NO es BINs
                        $registro['requiere_reposicion'] = 0;
                        $registro['cantidad_bins'] = 0;
                    }

                    if ($registro['requiere_reposicion']) { // SI requiere reposición ...

                        if (rand(1, 100) <= 20) { // 20% de probabilidad de cambiar cantidad_bins
                            $nueva_cantidad = $registro['cantidad_bins'];
                            while($nueva_cantidad == $registro['cantidad_bins']){
                                $nueva_cantidad = rand(1, 30);
                            }

                            $registro['cantidad_bins'] = $nueva_cantidad;
                            $huboCambio = true;
                        }

                    } else { // NO requiere reposición
                        $registro['cantidad_bins'] = 0;
                    }
                }

                // Aplicar reglas finales
                // if ($registro['requiere_reposicion'] == 0) {
                //    $registro['cantidad_bins'] = 0;
                // }

                DB::table('retiros_historial')->insert([
                    'retiro_id'           => $retiro->id,
                    'fecha_retiro'        => $registro['fecha_retiro']->format('Y-m-d H:i:s'),
                    'tipo_retiro_id'      => $registro['tipo_retiro_id'],
                    'kilogramos_estimados'=> round($registro['kilogramos_estimados'], 2),
                    'requiere_reposicion' => $registro['requiere_reposicion'],
                    'cantidad_bins'       => $registro['cantidad_bins'],
                    'estado_id'           => $retiro->estado_id,
                    'activo'              => $retiro->activo,
                    'created_at'          => $fechas[$i]->format('Y-m-d H:i:s'),
                    'usuario_id'          => 2,
                    'motivo_cambio'       => Arr::random($motivos),
                ]);
            }

            // ✅ LOG POR CONSOLA AQUÍ
            $this->command->info("Historial generado para retiro ID {$retiro->id}: {$cantidadHistorial} registros.");
        }
    }
}
