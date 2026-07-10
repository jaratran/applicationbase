<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReasignarCamionesSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener empresas transportistas disponibles
        $empresasTransportistas = DB::table('empresas')
            ->where('tipo_empresa_id', 33)
            ->pluck('id')
            ->toArray();

        if (empty($empresasTransportistas)) {
            $this->command->warn('No se encontraron empresas transportistas (tipo_empresa_id = 33).');
            return;
        }

        // Obtener camiones asociados actualmente a empresas de tipo 32
        $camiones = DB::table('camiones as c')
            ->join('empresas as e', 'e.id', '=', 'c.empresa_id')
            ->where('e.tipo_empresa_id', 32)
            ->select('c.id')
            ->get();

        foreach ($camiones as $camion) {
            $nuevaEmpresaId = $empresasTransportistas[array_rand($empresasTransportistas)];

            DB::table('camiones')
                ->where('id', $camion->id)
                ->update(['empresa_id' => $nuevaEmpresaId]);
        }

        $this->command->info('Camiones reasignados exitosamente a empresas transportistas.');
    }
}
