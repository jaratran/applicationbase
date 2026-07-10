<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReasignarConductoresSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener IDs de empresas transportistas (tipo_empresa_id = 33)
        $empresasTransportistas = DB::table('empresas')
            ->where('tipo_empresa_id', 33)
            ->pluck('id')
            ->toArray();

        if (empty($empresasTransportistas)) {
            $this->command->warn('No se encontraron empresas transportistas (tipo_empresa_id = 33).');
            return;
        }

        // Obtener conductores actuales que están asignados a empresas tipo 32
        $conductores = DB::table('conductores as c')
            ->join('empresas as e', 'e.id', '=', 'c.empresa_id')
            ->where('e.tipo_empresa_id', 32)
            ->select('c.id')
            ->get();

        foreach ($conductores as $conductor) {
            $nuevaEmpresaId = $empresasTransportistas[array_rand($empresasTransportistas)];

            DB::table('conductores')
                ->where('id', $conductor->id)
                ->update(['empresa_id' => $nuevaEmpresaId]);
        }

        $this->command->info('Conductores reasignados a empresas transportistas exitosamente.');
    }
}
