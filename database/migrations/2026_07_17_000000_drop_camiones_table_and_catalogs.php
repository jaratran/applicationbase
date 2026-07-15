<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('camiones')) {
            $foreignKeys = DB::table('information_schema.KEY_COLUMN_USAGE')
                ->where('REFERENCED_TABLE_SCHEMA', DB::getDatabaseName())
                ->where('REFERENCED_TABLE_NAME', 'camiones')
                ->whereNotNull('CONSTRAINT_NAME')
                ->get(['TABLE_NAME', 'CONSTRAINT_NAME']);

            foreach ($foreignKeys as $foreignKey) {
                $table = str_replace('`', '``', $foreignKey->TABLE_NAME);
                $constraint = str_replace('`', '``', $foreignKey->CONSTRAINT_NAME);
                DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraint}`");
            }

            Schema::drop('camiones');
        }

        if (!Schema::hasTable('catalogos')) {
            return;
        }

        $categoryIds = [43, 53];
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
        // Poda irreversible desde ApplicationBase: recuperar Camiones exige restaurar
        // coordinadamente su código, esquema, catálogos y datos operacionales.
    }
};
