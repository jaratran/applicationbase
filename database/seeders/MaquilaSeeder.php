<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Sucursal;
use App\Models\Empresa;

class MaquilaSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $empresas = Empresa::pluck('id')->toArray(); // Lista total de empresas disponibles
        $sucursales = Sucursal::pluck('id')->toArray(); // Lista total de sucursales

        $insertados = [];

        /**
         * 🏭 Fase 1 — Recorrer todas las sucursales y asignarles entre 1 y 10 maquilas
         */
        foreach ($sucursales as $sucursalId) {
            $cantidad = rand(1, 10);

            // Elegir empresas aleatorias para esta sucursal (sin repetir)
            $empresasAsignadas = collect($empresas)->random($cantidad);

            foreach ($empresasAsignadas as $empresaId) {
                // Evitar duplicados (por si se repiten combinaciones en otras fases)
                $clave = $empresaId . '-' . $sucursalId;
                if (!isset($insertados[$clave])) {
                    DB::table('maquilas')->insert([
                        'empresa_id'   => $empresaId,
                        'sucursal_id'  => $sucursalId,
                        'fecha_inicio' => now()->subDays(rand(0, 365)),
                        'activo'       => true,
                        'observaciones'=> null,
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ]);
                    $insertados[$clave] = true;
                }
            }
        }

        /**
         * 🧪 Fase 2 — Revisar empresas que aún no están en ninguna maquila
         */
        $empresasConMaquilas = DB::table('maquilas')->pluck('empresa_id')->unique()->toArray();
        $empresasSinMaquilas = array_diff($empresas, $empresasConMaquilas);

        foreach ($empresasSinMaquilas as $empresaId) {
            $cantidad = rand(1, 5);

            // Elegir sucursales aleatorias para esta empresa
            $sucursalesAsignadas = collect($sucursales)->random($cantidad);

            foreach ($sucursalesAsignadas as $sucursalId) {
                $clave = $empresaId . '-' . $sucursalId;
                if (!isset($insertados[$clave])) {
                    DB::table('maquilas')->insert([
                        'empresa_id'   => $empresaId,
                        'sucursal_id'  => $sucursalId,
                        'fecha_inicio' => now()->subDays(rand(0, 365)),
                        'activo'       => true,
                        'observaciones'=> null,
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ]);
                    $insertados[$clave] = true;
                }
            }
        }

        $total = count($insertados);
        echo "\n✅ Se insertaron $total maquilas únicas (empresa-sucursal).\n";
    }
}
