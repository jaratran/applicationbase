<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Empresa;
use Illuminate\Support\Str;

class EmpresaSeeder extends Seeder
{
    public function run(): void
    {
        $sucursales = DB::table('sucursales')->where('activo', true)->pluck('id')->toArray();
        $tiposEmpresa = DB::table('list_parameters')->where('Tipo', 504)->pluck('ID')->toArray();
        $comunas = DB::table('comuna')->whereIn('region_comuna', [9, 10, 11, 12, 14])->pluck('id')->toArray();

        $nombres = [
            'Acuícola', 'Salmonera', 'Piscícola', 'Marítima', 'Cultivos', 'AguaMar', 'BioMarina', 'Oceanía',
            'Fiordos', 'Puerto Azul', 'Aqua Andes', 'EcoFish', 'SurAcuícola', 'Lagos Chile', 'Andes Mar',
            'Patagonia Blue', 'TruchaViva', 'SurTech', 'Agua Clara', 'Viento Austral'
        ];

        $sufijos = ['S.A.', 'Ltda.', 'SpA', 'EIRL', 'y Cía.', 'Chile', 'Austral', 'del Sur', 'Group', 'Holding'];

        $inactivas = collect([3, 12, 21, 29, 38, 47, 56, 67, 84, 98]);
        $razonesUsadas = [];

        for ($i = 1; $i <= 110; $i++) {
            // Generar razón social única
            do {
                $base = fake()->randomElement($nombres);
                $extra = fake()->city();
                $sufijo = fake()->randomElement($sufijos);
                $razonSocial = "{$base} {$extra} {$sufijo}";
            } while (in_array($razonSocial, $razonesUsadas));
            $razonesUsadas[] = $razonSocial;

            $rutNumero = rand(60000000, 89999999);
            $rutDv = $this->calcularDv($rutNumero);
            $rut = number_format($rutNumero, 0, '', '.') . '-' . $rutDv;

            Empresa::create([
                'sucursal_id'            => fake()->randomElement($sucursales),
                'tipo_empresa_id'        => fake()->randomElement($tiposEmpresa),
                'rut_empresa'            => $rut,
                'razon_social'           => $razonSocial,
                'direccion'              => 'Av. ' . fake()->streetName() . ' Nº' . fake()->numberBetween(100, 9999),
                'comuna_id'              => fake()->randomElement($comunas),
                'telefono'               => '9000000' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'email_contacto'         => Str::slug($razonSocial, '_') . '@chile.cl',
                'telefono_contacto'      => '9000000' . str_pad(rand(1, 99), 2, '0', STR_PAD_LEFT),
                'activo'                 => !$inactivas->contains($i),
                'observacion_inactividad'=> $inactivas->contains($i) ? 'Empresa actualmente inactiva' : null,
                'created_at'             => now(),
                'updated_at'             => now(),
            ]);
        }
    }

    private function calcularDv($rut)
    {
        $suma = 0;
        $multiplo = 2;

        while ($rut > 0) {
            $suma += ($rut % 10) * $multiplo;
            $rut = intval($rut / 10);
            $multiplo = $multiplo == 7 ? 2 : $multiplo + 1;
        }

        $resto = $suma % 11;
        $dv = 11 - $resto;

        if ($dv == 11) return '0';
        if ($dv == 10) return 'K';
        return (string) $dv;
    }
}
