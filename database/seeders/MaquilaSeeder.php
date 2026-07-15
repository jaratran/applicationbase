<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class MaquilaSeeder extends Seeder
{
    public function run(): void
    {
        $empresaId = DB::table('empresas')->where('razon_social', 'Empresa Productora Base')->value('id');
        $sucursalId = DB::table('sucursales')->where('nombre_sucursal', 'Sucursal Principal')->value('id');

        if ($empresaId === null || $sucursalId === null) {
            throw new RuntimeException('Faltan la empresa o sucursal base requeridas para crear la maquila.');
        }

        DB::table('maquilas')->updateOrInsert(
            ['empresa_id' => $empresaId, 'sucursal_id' => $sucursalId],
            [
                'fecha_inicio' => now()->toDateString(),
                'activo' => true,
                'observaciones' => null,
                'updated_at' => now(),
            ]
        );
    }
}
