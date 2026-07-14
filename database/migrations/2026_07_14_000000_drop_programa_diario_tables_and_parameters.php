<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('programas_diarios_detalle');
        Schema::dropIfExists('programas_diarios');

        if (Schema::hasTable('operational_parameters')) {
            $columns = array_values(array_filter([
                'daily_program_execution_time',
                'auto_emit_daily_program',
            ], fn (string $column) => Schema::hasColumn('operational_parameters', $column)));

            if ($columns !== []) {
                Schema::table('operational_parameters', function (Blueprint $table) use ($columns) {
                    $table->dropColumn($columns);
                });
            }
        }
    }

    public function down(): void
    {
        // Poda irreversible desde ApplicationBase: revertirla responsablemente exige restaurar
        // en conjunto el código, el esquema y, si corresponde, los datos históricos de La Portada.
        // Esta migración no reconstruye una capacidad parcial ni tablas vacías incompatibles.
    }
};
