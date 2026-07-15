<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class EmpresaSeeder extends Seeder
{
    public function run(): void
    {
        $comunaId = DB::table('comunas')->where('nombre', 'No especificado')->value('id');
        $tipos = DB::table('catalogos')
            ->whereIn('nombre', ['Productor', 'Transportista'])
            ->pluck('id', 'nombre');

        if ($comunaId === null || !$tipos->has('Productor') || !$tipos->has('Transportista')) {
            throw new RuntimeException('Faltan catálogos o ubicación base requeridos para crear empresas.');
        }

        foreach ([
            ['11.111.111-1', 'Empresa Productora Base', 'Productor'],
            ['22.222.222-2', 'Empresa Transportista Base', 'Transportista'],
        ] as [$rut, $razonSocial, $tipo]) {
            DB::table('empresas')->updateOrInsert(
                ['rut_empresa' => $rut],
                [
                    'tipo_empresa_id' => $tipos[$tipo],
                    'razon_social' => $razonSocial,
                    'direccion' => 'No especificada',
                    'comuna_id' => $comunaId,
                    'telefono' => null,
                    'email_contacto' => null,
                    'telefono_contacto' => null,
                    'activo' => true,
                    'observacion_inactividad' => null,
                    'updated_at' => now(),
                ]
            );
        }
    }
}
