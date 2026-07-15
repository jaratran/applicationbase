<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SucursalSeeder extends Seeder
{
    public function run(): void
    {
        $zonaId = $this->catalogoId('Zona de Sucursal', 'Puerto Montt');
        $tipoId = $this->catalogoId('Tipo de Sucursal', 'Planta');
        $comunaId = DB::table('comunas')->where('nombre', 'No especificado')->value('id');

        if ($comunaId === null) {
            throw new RuntimeException('No existe la comuna base requerida para crear sucursales.');
        }

        foreach ([
            ['Sucursal Principal', 10001, 'principal@example.com'],
            ['Sucursal Secundaria', 10002, 'secundaria@example.com'],
        ] as [$nombre, $codigo, $email]) {
            DB::table('sucursales')->updateOrInsert(
                ['nombre_sucursal' => $nombre],
                [
                    'zona_id' => $zonaId,
                    'codigo_siep' => $codigo,
                    'tipo_sucursal_id' => $tipoId,
                    'comuna_id' => $comunaId,
                    'telefono' => null,
                    'email' => $email,
                    'km' => null,
                    'tiempo_estimado_viaje' => null,
                    'activo' => true,
                    'observacion_inactividad' => null,
                    'updated_at' => now(),
                ]
            );
        }
    }

    private function catalogoId(string $categoria, string $valor): int
    {
        $categoriaId = DB::table('catalogos')
            ->whereNull('catalogo_id')
            ->where('nombre', $categoria)
            ->value('id');

        $id = DB::table('catalogos')
            ->where('catalogo_id', $categoriaId)
            ->where('nombre', $valor)
            ->value('id');

        if ($id === null) {
            throw new RuntimeException("No existe el catálogo {$categoria} / {$valor}.");
        }

        return (int) $id;
    }
}
