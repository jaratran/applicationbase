<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Maquila;
use App\Models\Empresa;
use App\Models\Catalogo;

class CorregirEmpresasEnMaquilas extends Command
{
    protected $signature = 'maquilas:corregir-empresas {--dry-run : Ejecuta la simulación sin guardar cambios}';

    protected $description = 'Corrige las maquilas asociadas erróneamente a empresas transportistas, reemplazándolas por empresas productoras válidas en la misma sucursal.';

    public function handle(): void
    {
        $TIPO_PRODUCTORA    = config('constantes.TIPO_EMPRESA_PRODUCTORA');
        $TIPO_TRANSPORTISTA = config('constantes.TIPO_EMPRESA_TRANSPORTISTA');

        $maquilasErroneas = Maquila::with('empresa', 'sucursal')
            ->whereHas('empresa', fn ($q) => $q->where('tipo_empresa_id', $TIPO_TRANSPORTISTA))
            ->get();

        if ($maquilasErroneas->isEmpty()) {
            $this->info('✅ No se encontraron maquilas con empresas transportistas.');
            return;
        }

        $this->warn("Se encontraron {$maquilasErroneas->count()} maquilas con empresas transportistas.");

        foreach ($maquilasErroneas as $maquila) {
            $sucursalId = $maquila->sucursal_id;

            // Buscar empresas productoras disponibles no vinculadas aún a esta sucursal
            $empresasVinculadas = Maquila::where('sucursal_id', $sucursalId)->pluck('empresa_id')->toArray();

            $candidatas = Empresa::where('tipo_empresa_id', $TIPO_PRODUCTORA)
                ->whereNotIn('id', $empresasVinculadas)
                ->get();

            if ($candidatas->isEmpty()) {
                $this->error("❌ Sucursal ID {$sucursalId} - No hay empresas productoras disponibles.");
                continue;
            }

            $nuevaEmpresa = $candidatas->random(); // Puedes aplicar lógica más compleja aquí

            $this->line("🛠️ Maquila ID {$maquila->id} | Sucursal {$sucursalId}");
            $this->line("   Reemplazar: {$maquila->empresa->razon_social} (Transportista)");
            $this->line("        por: {$nuevaEmpresa->razon_social} (Productora)");

            if (!$this->option('dry-run')) {
                $maquila->empresa_id = $nuevaEmpresa->id;
                $maquila->save();
            }
        }

        if ($this->option('dry-run')) {
            $this->info('🧪 Modo simulación activado (--dry-run), no se guardaron cambios.');
        } else {
            $this->info('✅ Corrección completada con éxito.');
        }
    }
}
