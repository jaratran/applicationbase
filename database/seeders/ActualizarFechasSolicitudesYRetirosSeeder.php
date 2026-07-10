<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ActualizarFechasSolicitudesYRetirosSeeder extends Seeder
{
    public function run(): void
    {
        $fechaInicio = Carbon::create(2025, 2, 1);
        $fechaFin    = Carbon::create(2025, 6, 1);
        $fechas      = collect();

        $this->command->info("Fecha de inicio : {$fechaInicio}.");
        $this->command->info("Fecha de fin    : {$fechaFin}.");

        // Actualizar fechas en SOLICITUDES
        $solicitudes = DB::table('solicitudes')
                            ->orderBy('id') // ← ascendente por defecto
                            ->get();

        $cantidadSolicitudes = $solicitudes->count();
        $this->command->info("q Solicitudes   : {$cantidadSolicitudes}.");

        // Generamos las fechas de las solicitudes
        $lapso      = $fechaInicio->diffInMinutes($fechaFin);
        $incremento = intval($lapso / ($cantidadSolicitudes - 1));
        $this->command->info("lapso           : {$lapso}.");
        $this->command->info("incremento      : {$incremento}.");

        for ($i = 0; $i < $cantidadSolicitudes; $i++) {
            $fechas->push($fechaInicio->copy()->addMinutes($incremento * $i));
        }

        $i = 0;
        foreach ($solicitudes as $solicitud) {
            // Recupera la fecha para solicitud del listado de fechas
            $fechaSolicitud = $fechas[$i]->format('Y-m-d H:i:s');

            $this->command->info("$solicitud->id      : {$solicitud->id}.");
            $this->command->info("$fechaSolicitud     : {$fechaSolicitud}.");

            // Actualizar created_at y updated_at de la solicitud
            DB::table('solicitudes')
                ->where('id', $solicitud->id)
                ->update([
                    'created_at' => $fechaSolicitud,
                    'updated_at' => $fechaSolicitud,
                ]);

            // Actualizar retiros que dependen de esta solicitud
            $retiros = DB::table('retiros')->where('solicitud_id', $solicitud->id)->get();

            foreach ($retiros as $retiro) {
                $fechaRetiro = Carbon::parse($fechaSolicitud)->addDays(rand(1, 10));

                DB::table('retiros')
                    ->where('id', $retiro->id)
                    ->update([
                        'fecha_retiro' => $fechaRetiro->format('Y-m-d H:i:s'),
                        'created_at'   => Carbon::parse($fechaSolicitud)->format('Y-m-d H:i:s'),
                        'updated_at'   => Carbon::parse($fechaSolicitud)->format('Y-m-d H:i:s'),
                    ]);
            }

            $i++;
        }
    }
}
