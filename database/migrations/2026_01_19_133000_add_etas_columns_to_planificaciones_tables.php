<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::table('planificaciones', function (Blueprint $table) {
			$table->dateTime('eta_calculada')->nullable()->after('hora_llegada_estimada');

			$table->unsignedBigInteger('eta_motivo_modificacion_id')->nullable()->after('eta_calculada');
			$table->index('eta_motivo_modificacion_id', 'planificaciones_eta_motivo_modificacion_id_idx');
			$table->foreign('eta_motivo_modificacion_id', 'planificaciones_eta_motivo_modificacion_id_fk')->references('id')->on('catalogos');
		});
	}

	public function down(): void
	{
		Schema::table('planificaciones', function (Blueprint $table) {
			$table->dropForeign('planificaciones_eta_motivo_modificacion_id_fk');
			$table->dropIndex('planificaciones_eta_motivo_modificacion_id_idx');
			$table->dropColumn('eta_motivo_modificacion_id');

			$table->dropColumn('eta_calculada');
		});
	}
};
