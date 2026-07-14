<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('planificaciones');

        if (Schema::hasTable('operational_parameters')) {
            $columns = array_values(array_filter([
                'average_truck_speed',
                'maritime_transit_duration_days',
                'terrestrial_transit_duration_days',
                'combined_transit_duration_days',
                'delay_arribo_eta_hours',
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
        // Poda irreversible desde ApplicationBase: recuperar Planificaciones exige restaurar
        // coordinadamente su código, esquema y datos históricos del dominio de La Portada.
    }
};
