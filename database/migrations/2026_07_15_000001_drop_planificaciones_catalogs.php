<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('catalogos')) {
            return;
        }

        $categoryIds = [110, 190];
        $catalogIds = DB::table('catalogos')
            ->whereIn('id', $categoryIds)
            ->orWhereIn('catalogo_id', $categoryIds)
            ->pluck('id');

        if ($catalogIds->isEmpty()) {
            return;
        }

        if (Schema::hasTable('catalogo_relaciones')) {
            DB::table('catalogo_relaciones')
                ->whereIn('valor_origen_id', $catalogIds)
                ->orWhereIn('valor_destino_id', $catalogIds)
                ->orWhereIn('tipo_relacion_id', $catalogIds)
                ->delete();
        }

        DB::table('catalogos')->whereIn('id', $catalogIds)->delete();
    }

    public function down(): void
    {
        // Poda irreversible: los motivos solo son válidos junto con la capacidad
        // de Planificaciones y no se reconstruyen como parámetros aislados.
    }
};
