<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Retiro;

class PlanificacionesDesdeRetirosSeeder extends Seeder
{
    public function run(): void
    {
        $retiros = Retiro::where('estado_id', config('constantes.ESTADO_RETIRO_ACEPTADO'))
                        ->where('activo', true)
                        ->doesntHave('planificacion')
                        ->get();

        $total = 0;

        foreach ($retiros as $retiro) {
            $retiro->crearPlanificacionInicial();
            $total++;
        }

        $this->command->info("✅ Se generaron $total planificaciones vacías para retiros aprobados.");
    }
}
