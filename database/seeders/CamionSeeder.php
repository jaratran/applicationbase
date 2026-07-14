<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Camion;

class CamionSeeder extends Seeder
{
    public function run(): void
    {
        // Empresas activas
        $empresas = DB::table('empresas')->where('activo', true)->pluck('id')->toArray();

        // Conductores activos agrupados por empresa
        $conductoresPorEmpresa = DB::table('conductores')
            ->where('activo', true)
            ->get()
            ->groupBy('empresa_id')
            ->map(fn ($grupo) => $grupo->pluck('id')->toArray())
            ->toArray();

        // Tipos de camión
        $tiposCamion = DB::table('list_parameters')->where('Tipo', 506)->pluck('ID')->toArray();

        // Comentarios simpáticos para inactivos
        $comentarios = [
            'Se transformó en Optimus Prime y huyó.',
            'Lo secuestraron para una película de acción.',
            'Ahora transporta unicornios imaginarios.',
            'Estacionado esperando que bajen los combustibles.',
            'En proceso de transformarse en food truck de sushi.',
            'Se aburrió de la carretera y se fue a la playa.',
            'Camión en huelga por mejores rutas.',
            'Lo usan para clases de meditación con motor apagado.',
            'Está haciendo el camino de Santiago... pero en reversa.',
            'Fue a buscar pan y no volvió.',
            'En reparación por exceso de cumbia a bordo.',
            'Ahora vive en una comunidad ecológica sobre ruedas.',
            'Convierte CO₂ en memes.',
            'El camión pidió vacaciones sin goce de diesel.',
            'Se unió a un reality de transporte extremo.',
            'Participa en carreras clandestinas de acoplados.',
            'Hace Uber de cargas en paralelo.',
            'Tiene crisis existencial: quiere ser lancha.',
            'Le dio un infarto al GPS.',
            'Trabaja sólo los días con luna llena.'
        ];

        $inactivos = collect([3, 12, 21, 28, 37, 45, 58, 69, 78, 84, 91, 103, 112, 118, 124, 130, 135, 139, 144, 149]);

        for ($i = 1; $i <= 150; $i++) {
            // Seleccionar una empresa activa que tenga conductores
            do {
                $empresa_id = fake()->randomElement($empresas);
            } while (empty($conductoresPorEmpresa[$empresa_id]));

            // Seleccionar un conductor de esa empresa
            $conductor_id = fake()->randomElement($conductoresPorEmpresa[$empresa_id]);

            $patente = $this->generarPatente($i);

            $esInactivo = $inactivos->contains($i);
            $observacion = $esInactivo ? $comentarios[($i - 1) % count($comentarios)] : null;

            Camion::create([
                'empresa_id'             => $empresa_id,
                'conductor_id'           => $conductor_id,
                'tipo_camion_id'         => fake()->randomElement($tiposCamion),
                'patente'                => $patente,
                'arrendado'              => rand(1, 100) <= 20,
                'rendimiento_optimo'     => fake()->randomFloat(2, 2.5, 4.5),
                'activo'                 => !$esInactivo,
                'observacion_inactividad'=> $observacion,
                'created_at'             => now(),
                'updated_at'             => now(),
            ]);
        }
    }

    private function generarPatente($semilla)
    {
        if ($semilla % 2 === 0) {
            // Formato clásico: AB-1234
            return strtoupper(fake()->randomLetter() . fake()->randomLetter()) . '-' . str_pad($semilla % 10000, 4, '0', STR_PAD_LEFT);
        } else {
            // Formato nuevo: WXYZ-12
            return strtoupper(fake()->randomLetter() . fake()->randomLetter() . fake()->randomLetter() . fake()->randomLetter()) . '-' . str_pad($semilla % 100, 2, '0', STR_PAD_LEFT);
        }
    }
}
