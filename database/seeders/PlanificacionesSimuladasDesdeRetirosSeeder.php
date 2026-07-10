<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Retiro;
use App\Models\Camion;
use App\Models\Conductor;
use App\Models\Planificacion;
use App\Models\Catalogo;
use Carbon\Carbon;

class PlanificacionesSimuladasDesdeRetirosSeeder extends Seeder
{
    public function run(): void
    {
        // $retiros = Retiro::with(['solicitud.maquila.empresa', 'planificacion'])
        //                 ->where('estado_id', config('constantes.ESTADO_RETIRO_PLANIFICADO'))
        //                 ->where('activo', true)
        //                 ->doesntHave('planificacion')
        //                 ->get();

        // $retiros = Retiro::with(['solicitud.maquila.empresa', 'planificacion'])
        //                 ->where('estado_id', config('constantes.ESTADO_RETIRO_PROGRAMADO'))
        //                 ->where('activo', true)
        //                 ->where('id', '!=', 475) // excluye el retiro específico
        //                 ->doesntHave('planificacion')
        //                 ->get();

        $retiros = Retiro::with(['solicitud.maquila.empresa', 'planificacion'])
                        ->where('estado_id', config('constantes.ESTADO_RETIRO_TERMINADO'))
                        ->where('activo', true)
                        ->doesntHave('planificacion')
                        ->get();

        $tiposEspecie = Catalogo::where('catalogo_id', config('constantes.CATEGORIA_TIPO_ESPECIE'))->pluck('id')->all();
        $tiposMateria = Catalogo::where('catalogo_id', config('constantes.CATEGORIA_TIPO_MATERIA_PRIMA'))->pluck('id')->all();
        $motivosCambio = Catalogo::where('catalogo_id', config('constantes.CATEGORIA_CAMBIOS_PLANIFICACION'))->pluck('id')->all();

        $total = 0;

        foreach ($retiros as $retiro) {
            $camiones = Camion::all();
            if ($camiones->isEmpty()) continue;

            $camion = $camiones->random();

            $conductores = Conductor::where('empresa_id', $camion->empresa_id)->pluck('id')->all();
            if (empty($conductores)) continue;

            $usarConductorDelCamion = rand(0, 1) === 1;
            $conductorId = $usarConductorDelCamion ? $camion->conductor_id : collect($conductores)->filter(fn($id) => $id !== $camion->conductor_id)->random();

            // Fecha para cuando se había solicitado el retiro
            $fechaDeseada = Carbon::parse($retiro->fecha_retiro);
            $fechaSolicitud = Carbon::parse($retiro->solicitud->fecha_solicitud);

            // 20% de suerte
            if (rand(1, 100) <= 20) {
                $fechaPlanificada = $fechaDeseada->copy();
            } else {
                $min = $fechaSolicitud->copy()->addHours(6);
                $max = $fechaDeseada->copy()->addDays(7);
                do {
                    $fechaPlanificada = Carbon::createFromTimestamp(rand($min->timestamp, $max->timestamp));
                } while ($fechaPlanificada->lessThanOrEqualTo($fechaDeseada));
            }

            // Duración entre 1:30 y 6:30
            $duracionMin = 90;  // minutos
            $duracionMax = 390; // minutos
            $duracionMinutos = rand($duracionMin, $duracionMax);
            $duracionTexto = sprintf('%02d:%02d', intdiv($duracionMinutos, 60), $duracionMinutos % 60);

            $horaLlegada = $fechaPlanificada->copy()->addMinutes($duracionMinutos);

            // Patente rampla: 80% tiene
            $patenteRampla = (rand(1, 100) <= 80) ? self::generarPatente() : null;

            // Motivo modificación: 50% tiene
            $motivoId = (rand(0, 1) === 1 && !empty($motivosCambio)) ? collect($motivosCambio)->random() : null;

            // Timestamp de actualización realista
            $updatedAt = $fechaPlanificada->copy()->subDays(rand(2, 5));

            Planificacion::create([
                'retiro_id'               => $retiro->id,
                'fecha_hora_planificada'  => $fechaPlanificada,
                'duracion_viaje'          => $duracionTexto,
                'hora_llegada_estimada'   => $horaLlegada,
                'especie_id'              => collect($tiposEspecie)->random(),
                'tiene_restriccion'       => (bool) rand(0, 1),                     // 50% de restricciones
                'tipo_materia_prima_id'   => collect($tiposMateria)->random(),
                'camion_id'               => $camion->id,
                'patente_rampla'          => $patenteRampla,
                'conductor_id'            => $conductorId,
                'motivo_modificacion_id'  => $motivoId,
                'estado_id'               => $retiro->estado_id,
                'ticket_cierre'           => 'TKT-' . strtoupper(Str::random(6)),
                'activo'                  => true,
                'created_at'              => $retiro->updated_at,
                'updated_at'              => $updatedAt,
            ]);

            $total++;
        }

        $this->command->info("✅ Se generaron $total planificaciones simuladas realistas.");
    }

    private static function generarPatente(): string
    {
        $format = rand(0, 1);

        if ($format === 0) {
            // AB-1234
            $letras  = strtoupper(Str::random(2));
            $numeros = str_pad((string)rand(0, 9999), 4, '0', STR_PAD_LEFT);
        } else {
            // ABCD-12
            $letras  = strtoupper(Str::random(4));
            $numeros = str_pad((string)rand(0, 99), 2, '0', STR_PAD_LEFT);
        }

        return "{$letras}-{$numeros}";
    }
}
