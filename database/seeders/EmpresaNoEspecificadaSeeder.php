<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmpresaNoEspecificadaSeeder extends Seeder
{
    public function run(): void
    {
        // Insert con nombre excéntrico
        DB::table('empresas')->insert([
            'tipo_empresa_id'         => 0,
            'rut_empresa'             => '00.000.000-0',
            'razon_social'            => '***TRANSPORTISTA***NO***ESPECIFICADO***',
            'direccion'               => 'SIN DIRECCIÓN',
            'comuna_id'               => 0,
            'telefono'                => null,
            'email_contacto'          => null,
            'telefono_contacto'       => null,
            'activo'                  => true,
            'observacion_inactividad' => null,
            'created_at'              => now(),
            'updated_at'              => now(),
        ]);

        // Update a ID = 0
        DB::update("UPDATE empresas SET id = 0, razon_social = 'TRANSPORTISTA NO ESPECIFICADO' WHERE razon_social = '***TRANSPORTISTA***NO***ESPECIFICADO***'");

        echo "✅ Empresa genérica con ID = 0 insertada y normalizada.\n";
    }
}
