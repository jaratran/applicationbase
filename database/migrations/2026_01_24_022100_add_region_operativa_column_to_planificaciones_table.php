<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::table('planificaciones', function (Blueprint $table) {
			$table->unsignedBigInteger('region_operativa_id')->nullable()->after('retiro_id');
			$table->index('region_operativa_id', 'planificaciones_region_operativa_id_idx');
			$table->foreign('region_operativa_id', 'planificaciones_region_operativa_id_fk')->references('id')->on('regiones');
		});
	}

	public function down(): void
	{
		Schema::table('planificaciones', function (Blueprint $table) {
			$table->dropForeign('planificaciones_region_operativa_id_fk');
			$table->dropIndex('planificaciones_region_operativa_id_idx');
			$table->dropColumn('region_operativa_id');
		});
	}
};
