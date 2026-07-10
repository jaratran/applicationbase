<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programas_diarios_detalle', function (Blueprint $table) {
            $table->unsignedBigInteger('region_operativa_id')
                  ->nullable()
                  ->after('retiro_id')
                  ->index('programas_diarios_detalle_region_operativa_id_idx');
        });
    }

    public function down(): void
    {
        Schema::table('programas_diarios_detalle', function (Blueprint $table) {
            $table->dropIndex('programas_diarios_detalle_region_operativa_id_idx');
            $table->dropColumn('region_operativa_id');
        });
    }
};
