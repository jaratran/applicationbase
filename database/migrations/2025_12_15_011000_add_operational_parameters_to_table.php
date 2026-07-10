<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('operational_parameters', function (Blueprint $table) {
            $table->unsignedInteger('maritime_transit_duration_days')
                  ->default(3)
                  ->after('average_truck_speed');

            $table->unsignedInteger('terrestrial_transit_duration_days')
                  ->default(3)
                  ->after('maritime_transit_duration_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operational_parameters', function (Blueprint $table) {
            $table->dropColumn([
                'maritime_transit_duration_days',
                'terrestrial_transit_duration_days',
            ]);
        });
    }
};
