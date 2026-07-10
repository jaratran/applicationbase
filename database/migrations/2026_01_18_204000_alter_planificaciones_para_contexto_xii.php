<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::table('planificaciones', function (Blueprint $table) {

			// ------------------------------------------------------------------
			// Tipo de Transporte (Tierra / Barcaza / Combinado)
			// ------------------------------------------------------------------
			if (!Schema::hasColumn('planificaciones', 'tipo_transporte_id')) {
				$table->unsignedBigInteger('tipo_transporte_id')->nullable()->after('conductor_id');
				$table->index('tipo_transporte_id', 'planificaciones_tipo_transporte_id_idx');
				$table->foreign('tipo_transporte_id', 'planificaciones_tipo_transporte_id_fk')
					->references('id')->on('catalogos');
			}

			// ------------------------------------------------------------------
			// Rampla (FK nueva, se mantiene patente_rampla como legado)
			// ------------------------------------------------------------------
			if (!Schema::hasColumn('planificaciones', 'rampla_id')) {
				$table->unsignedBigInteger('rampla_id')->nullable()->after('tipo_transporte_id');
				$table->index('rampla_id', 'planificaciones_rampla_id_idx');
				$table->foreign('rampla_id', 'planificaciones_rampla_id_fk')
					->references('id')->on('ramplas');
			}

			// ------------------------------------------------------------------
			// Recursos de rescate desde Puerto Montt a Planta
			// ------------------------------------------------------------------
			if (!Schema::hasColumn('planificaciones', 'camion_rescate_id')) {
				$table->unsignedBigInteger('camion_rescate_id')->nullable()->after('camion_id');
				$table->index('camion_rescate_id', 'planificaciones_camion_rescate_id_idx');
				$table->foreign('camion_rescate_id', 'planificaciones_camion_rescate_id_fk')
					->references('id')->on('camiones');
			}

			if (!Schema::hasColumn('planificaciones', 'conductor_rescate_id')) {
				$table->unsignedBigInteger('conductor_rescate_id')->nullable()->after('camion_rescate_id');
				$table->index('conductor_rescate_id', 'planificaciones_conductor_rescate_id_idx');
				$table->foreign('conductor_rescate_id', 'planificaciones_conductor_rescate_id_fk')
					->references('id')->on('conductores');
			}

			// ------------------------------------------------------------------
			// Fechas operativas del flujo XII
			// ------------------------------------------------------------------
			if (!Schema::hasColumn('planificaciones', 'fecha_embarque')) {
				$table->dateTime('fecha_embarque')->nullable()->after('fecha_hora_planificada');
			}

			if (!Schema::hasColumn('planificaciones', 'fecha_arribo_puerto')) {
				$table->dateTime('fecha_arribo_puerto')->nullable()->after('fecha_embarque');
			}

			if (!Schema::hasColumn('planificaciones', 'fecha_rescate_puerto')) {
				$table->dateTime('fecha_rescate_puerto')->nullable()->after('fecha_arribo_puerto');
			}

			// ------------------------------------------------------------------
			// Duración estimada en días (XII)
			// ------------------------------------------------------------------
			if (!Schema::hasColumn('planificaciones', 'duracion_estimada_dias')) {
				$table->unsignedTinyInteger('duracion_estimada_dias')->nullable()->after('duracion_viaje');
			}

			// ------------------------------------------------------------------
			// Estado operativo de la Rampla
			// ------------------------------------------------------------------
			if (!Schema::hasColumn('planificaciones', 'estado_rampla_id')) {
				$table->unsignedBigInteger('estado_rampla_id')->nullable()->after('estado_id');
				$table->index('estado_rampla_id', 'planificaciones_estado_rampla_id_idx');
				$table->foreign('estado_rampla_id', 'planificaciones_estado_rampla_id_fk')
					->references('id')->on('catalogos');
			}

			// ------------------------------------------------------------------
			// Liberación de recursos
			// ------------------------------------------------------------------
			if (!Schema::hasColumn('planificaciones', 'fecha_liberacion_recursos')) {
				$table->dateTime('fecha_liberacion_recursos')->nullable()->after('hora_llegada_estimada');
			}

			// ------------------------------------------------------------------
			// Auditoría
			// ------------------------------------------------------------------
			if (!Schema::hasColumn('planificaciones', 'planificado_por_user_id')) {
				$table->unsignedBigInteger('planificado_por_user_id')->nullable()->after('retiro_id');
				$table->index('planificado_por_user_id', 'planificaciones_planificado_por_user_id_idx');
				$table->foreign('planificado_por_user_id', 'planificaciones_planificado_por_user_id_fk')
					->references('id')->on('users');
			}
		});
	}

	public function down(): void
	{
		Schema::table('planificaciones', function (Blueprint $table) {

			if (Schema::hasColumn('planificaciones', 'planificado_por_user_id')) {
				$table->dropForeign('planificaciones_planificado_por_user_id_fk');
				$table->dropIndex('planificaciones_planificado_por_user_id_idx');
				$table->dropColumn('planificado_por_user_id');
			}

			if (Schema::hasColumn('planificaciones', 'fecha_liberacion_recursos')) {
				$table->dropColumn('fecha_liberacion_recursos');
			}

			if (Schema::hasColumn('planificaciones', 'estado_rampla_id')) {
				$table->dropForeign('planificaciones_estado_rampla_id_fk');
				$table->dropIndex('planificaciones_estado_rampla_id_idx');
				$table->dropColumn('estado_rampla_id');
			}

			if (Schema::hasColumn('planificaciones', 'duracion_estimada_dias')) {
				$table->dropColumn('duracion_estimada_dias');
			}

			if (Schema::hasColumn('planificaciones', 'fecha_rescate_puerto')) {
				$table->dropColumn('fecha_rescate_puerto');
			}

			if (Schema::hasColumn('planificaciones', 'fecha_arribo_puerto')) {
				$table->dropColumn('fecha_arribo_puerto');
			}

			if (Schema::hasColumn('planificaciones', 'fecha_embarque')) {
				$table->dropColumn('fecha_embarque');
			}

			if (Schema::hasColumn('planificaciones', 'conductor_rescate_id')) {
				$table->dropForeign('planificaciones_conductor_rescate_id_fk');
				$table->dropIndex('planificaciones_conductor_rescate_id_idx');
				$table->dropColumn('conductor_rescate_id');
			}

			if (Schema::hasColumn('planificaciones', 'camion_rescate_id')) {
				$table->dropForeign('planificaciones_camion_rescate_id_fk');
				$table->dropIndex('planificaciones_camion_rescate_id_idx');
				$table->dropColumn('camion_rescate_id');
			}

			if (Schema::hasColumn('planificaciones', 'rampla_id')) {
				$table->dropForeign('planificaciones_rampla_id_fk');
				$table->dropIndex('planificaciones_rampla_id_idx');
				$table->dropColumn('rampla_id');
			}

			if (Schema::hasColumn('planificaciones', 'tipo_transporte_id')) {
				$table->dropForeign('planificaciones_tipo_transporte_id_fk');
				$table->dropIndex('planificaciones_tipo_transporte_id_idx');
				$table->dropColumn('tipo_transporte_id');
			}
		});
	}
};
