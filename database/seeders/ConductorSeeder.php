<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Conductor;
use Illuminate\Support\Str;

class ConductorSeeder extends Seeder
{
    public function run(): void
    {
        $empresasActivas = DB::table('empresas')->where('activo', true)->pluck('id')->toArray();
        $inactivos = collect([7, 24, 39, 56, 72, 91, 109, 133, 148, 175, 204, 221, 239, 263, 287, 303, 326, 348, 371, 397]);

        $comentarios = [
            'Se fue a criar ovejas a la Patagonia.',
            'Actualmente explorando Marte con Elon Musk.',
            'Lo vieron por última vez vendiendo sopaipillas en la feria.',
            'Renunció para estudiar canto gregoriano.',
            'En sabático luego de ganar la lotería.',
            'Se convirtió en influencer de acuarios.',
            'Prefirió el mundo del circo.',
            'Cambió camión por kayak.',
            'Fundó su propia app de reparto de empanadas.',
            'En pausa laboral, criando alpacas.',
            'Dejó todo para buscar a Godzilla.',
            'Está en búsqueda espiritual en el Cajón del Maipo.',
            'Se fue a hacer stand up a Valdivia.',
            'Actualmente maestro de sushi itinerante.',
            'Ahora es DJ de cumbia subacuática.',
            'En entrenamiento para Ironman en Chiloé.',
            'Partió en busca del Tutankamón chilote.',
            'Ahora guía turístico en el lago Lanalhue.',
            'Se hizo streamer de camiones en Twitch.',
            'Se retiró feliz, con pensión de chiste.',
        ];

        for ($i = 1; $i <= 500; $i++) {
            // Alternar entre hombres y mujeres (80% hombres, 20% mujeres)
            if ($i % 5 === 0) {
                $nombre = fake('es_CL')->firstNameFemale();
                $apellido = fake('es_CL')->lastName();
            } else {
                $nombre = fake('es_CL')->firstNameMale();
                $apellido = fake('es_CL')->lastName();
            }

            // RUT válido
            $rutNumero = rand(1000000, 25000000);
            $rutDv = $this->calcularDv($rutNumero);
            $rut = number_format($rutNumero, 0, '', '.') . '-' . $rutDv;

            $esInactivo = $inactivos->contains($i);
            $observacion = $esInactivo ? $comentarios[($i - 1) % count($comentarios)] : null;

            Conductor::create([
                'empresa_id'             => fake()->randomElement($empresasActivas),
                'rut'                    => $rut,
                'nombre'                 => $nombre,
                'apellido'               => $apellido,
                'telefono'               => '9000000' . str_pad($i % 100, 2, '0', STR_PAD_LEFT),
                'activo'                 => !$esInactivo,
                'observacion_inactividad'=> $observacion,
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
