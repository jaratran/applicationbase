<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->dropForeignKeysReferencing('telegram_links');
        Schema::dropIfExists('telegram_links');

        $this->dropForeignKeysReferencing('conductores');
        Schema::dropIfExists('conductores');
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

    public function down(): void
    {
        // Poda irreversible desde ApplicationBase: recuperar Conductores y su
        // integración Telegram exige restaurar coordinadamente código, esquema y datos.
    }
};
