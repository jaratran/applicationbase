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
            $table->unsignedInteger('delay_arribo_eta_hours')
                ->default(3)
                ->after('combined_transit_duration_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operational_parameters', function (Blueprint $table) {
            $table->dropColumn('delay_arribo_eta_hours');
        });
    }
};
