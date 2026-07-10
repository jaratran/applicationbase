<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Planificacion;
use Illuminate\Support\Str;

class AjustarRestriccionesYPatenteRampla extends Command
{
    protected $signature = 'planificaciones:ajustar-restricciones-patente-rampla {--dry-run : Simula los cambios sin guardar}';

    protected $description = 'Ajusta los campos tiene_restriccion y patente_rampla en planificaciones existentes, asegurando formato y distribución 50/50.';

    public function handle(): void
    {
        $planificaciones = Planificacion::where('activo', true)->get();

        if ($planificaciones->isEmpty()) {
            $this->info('✅ No se encontraron planificaciones activas para ajustar.');
            return;
        }

        $total = 0;
        $actualizadas = 0;

        foreach ($planificaciones as $p) {
            $original = clone $p;

            // 50% de restricciones
            $p->tiene_restriccion = (bool) rand(0, 1);

            // Si tiene patente, la regeneramos con formato válido
            if (!is_null($p->patente_rampla)) {
                $p->patente_rampla = self::generarPatente();
            }

            $total++;

            if (!$this->option('dry-run')) {
                $p->save();
                $actualizadas++;
            }
        }

        if ($this->option('dry-run')) {
            $this->warn("🧪 Modo simulación activado. Se recorrerían $total planificaciones.");
        } else {
            $this->info("✅ Se actualizaron $actualizadas planificaciones activas.");
        }
    }

    private static function generarPatente(): string
    {
        $formato = rand(0, 1);

        if ($formato === 0) {
            // AB-1234
            $letras  = strtoupper(Str::random(2));
            $numeros = str_pad((string)rand(0, 9999), 4, '0', STR_PAD_LEFT);
        } else {
            // ABCD-12
            $letras  = strtoupper(Str::random(4));
            $numeros = str_pad((string)rand(0, 99), 2, '0', STR_PAD_LEFT);
        }

        return "{$letras}-{$numeros}";
    }
}
