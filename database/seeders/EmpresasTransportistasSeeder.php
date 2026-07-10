<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class EmpresasTransportistasSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_CL');

        // Obtener IDs de las empresas tipo transportista
        $empresas = DB::table('empresas')
            ->where('tipo_empresa_id', 33)
            ->select('id')
            ->get();

        foreach ($empresas as $empresa) {
            // Generar nombre ficticio y realista
            $nombre = $this->generarNombreTransportista($faker);

            // Actualizar razon_social
            DB::table('empresas')
                ->where('id', $empresa->id)
                ->update(['razon_social' => $nombre]);
        }
    }

    protected function generarNombreTransportista($faker): string
    {
        $formatos = [
            'Transportes :apellido: e Hijos Ltda.',
            'Fletes y Cargas :nombre: SpA',
            'Transporte :apellido: y Compañía',
            'Transportes del Norte :apellido: S.A.',
            'Servicios de Transporte :apellido: Hermanos Ltda.',
            'Transportes y Logística :apellido: SpA',
            'Camiones y Fletes :nombre: Ltda.',
        ];

        $formato = $faker->randomElement($formatos);
        $formato = str_replace(':nombre:', $faker->firstName, $formato);
        $formato = str_replace(':apellido:', $faker->lastName, $formato);

        return $formato;
    }
}
