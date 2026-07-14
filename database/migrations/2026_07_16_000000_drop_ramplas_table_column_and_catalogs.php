<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('camiones') && Schema::hasColumn('camiones', 'patente_rampla')) {
            Schema::table('camiones', function (Blueprint $table) {
                $table->dropColumn('patente_rampla');
            });
        }

        // La tabla debe retirarse antes de sus catálogos porque mantiene claves foráneas hacia ellos.
        Schema::dropIfExists('ramplas');

        if (!Schema::hasTable('catalogos')) {
            return;
        }

        $categoryIds = [150, 160, 170];
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
        // Poda irreversible desde ApplicationBase: recuperar Ramplas exige restaurar
        // coordinadamente su código, esquema, catálogos y datos operacionales.
    }
};
