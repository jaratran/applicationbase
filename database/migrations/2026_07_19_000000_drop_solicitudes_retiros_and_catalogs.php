<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['retiros_comentarios', 'retiros_historial', 'retiros', 'solicitudes'] as $table) {
            $this->dropForeignKeysReferencing($table);
            Schema::dropIfExists($table);
        }

        if (Schema::hasTable('operational_parameters')
            && Schema::hasColumn('operational_parameters', 'notify_admins_as_coordinators')) {
            Schema::table('operational_parameters', function (Blueprint $table) {
                $table->dropColumn('notify_admins_as_coordinators');
            });
        }

        $this->dropCatalogFamilies([34, 37, 41, 90, 140]);
    }

    private function dropForeignKeysReferencing(string $referencedTable): void
    {
        if (!Schema::hasTable($referencedTable)) {
            return;
        }

        $foreignKeys = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('REFERENCED_TABLE_SCHEMA', DB::getDatabaseName())
            ->where('REFERENCED_TABLE_NAME', $referencedTable)
            ->where('TABLE_NAME', '!=', $referencedTable)
            ->whereNotNull('CONSTRAINT_NAME')
            ->get(['TABLE_NAME', 'CONSTRAINT_NAME']);

        foreach ($foreignKeys as $foreignKey) {
            $table = str_replace('`', '``', $foreignKey->TABLE_NAME);
            $constraint = str_replace('`', '``', $foreignKey->CONSTRAINT_NAME);
            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraint}`");
        }
    }

    private function dropCatalogFamilies(array $categoryIds): void
    {
        if (!Schema::hasTable('catalogos')) {
            return;
        }

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
        // Poda irreversible desde ApplicationBase: recuperar Solicitudes y Retiros exige
        // restaurar coordinadamente su código, esquema, catálogos y datos operacionales.
    }
};
