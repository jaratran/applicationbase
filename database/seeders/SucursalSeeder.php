<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sucursal;
use Illuminate\Support\Facades\DB;

class SucursalSeeder extends Seeder
{
    public function run(): void
    {
        $zonas     = DB::table('list_parameters')->where('tipo', 501)->pluck('id')->toArray();
        $tipos     = DB::table('list_parameters')->where('tipo', 502)->pluck('id')->toArray();
        $ciudades  = DB::table('list_parameters')->where('tipo', 503)->pluck('id')->toArray();
        $comunas   = DB::table('comuna')->whereIn('region_comuna', [9, 10, 11, 12, 14])->pluck('id')->toArray();

        $nombres = [
            'Valle Hermoso', 'Monte Azul', 'Puerto Claro', 'Nueva Esperanza', 'San Benito',
            'Río Bueno', 'Altos del Sur', 'Costa Verde', 'Viento Norte', 'Los Aromos',
            'La Cumbre', 'Pehuén Mapu', 'Vista al Mar', 'Colina Sur', 'Cerro Negro',
            'El Totoral', 'Trigal Norte', 'Ñielol', 'Puerto Blanco', 'Aguas Claras',
            'Huapi Centro', 'Chillán Viejo', 'Lo Rojas', 'Santa Amalia', 'Quilamapu',
            'Rancho Grande', 'Tierra Noble', 'Los Maitenes', 'Punta Verde', 'Río Claro',
            'Villa Austral', 'Río Elqui', 'Bahía Serena', 'Monte Claro', 'Paso del Sol',
            'Trapananda', 'La Rinconada', 'Altos de Llanquihue', 'Antulafquén', 'Las Araucarias',
            'Tolhuaca', 'Pumalal', 'Valle Encantado', 'Villa Cordillera', 'Peulla', 
            'Puerto Encantado', 'Tierras Negras', 'Puerto Manso', 'Los Arrayanes', 'Las Gredas'
        ];

        foreach (range(1, 50) as $i) {
            $nombreSucursal = $nombres[$i - 1];
            Sucursal::create([
                'zona_id'                => fake()->randomElement($zonas),
                'nombre_sucursal'       => $nombreSucursal,
                'codigo_siep'           => fake()->numberBetween(10000, 99999),
                'tipo_sucursal_id'      => fake()->randomElement($tipos),
                'comuna_id'             => fake()->randomElement($comunas),
                'ciudad_id'             => fake()->randomElement($ciudades),
                'telefono'              => '9000000' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'email'                 => str_replace(' ', '_', strtolower($nombreSucursal)) . '@empresa.cl',
                'km'                    => fake()->numberBetween(2, 100),
                'tiempo_estimado_viaje'=> fake()->randomFloat(2, 0.5, 5.0),
                'activo'                => $i > 5, // Las 5 primeras inactivas
                'observacion_inactividad' => $i <= 5 ? 'Sucursal inactiva para pruebas' : null,
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);
        }
    }
}
