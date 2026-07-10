<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
	public function up(): void
	{
		// PASO 1: Crear la columna como nullable temporalmente (Región Operativa (XII / interregional))
		Schema::table('camiones', function (Blueprint $table) {
			$table->unsignedBigInteger('region_operativa_id')->nullable()->after('tipo_camion_id');
		});

		// PASO 2: Asignar valor por defecto a registros existentes (X Región)
		DB::table('camiones')->whereNull('region_operativa_id')->update([
			'region_operativa_id' => config('constantes.REGION_X') // 10 el ID de la décima en tabla Regiones
		]);

		// PASO 3: Modificar la columna para que no acepte nulos
		Schema::table('camiones', function (Blueprint $table) {
			$table->unsignedBigInteger('region_operativa_id')
				->nullable(false)  // Cambia a NOT NULL
				->default(config('constantes.REGION_NI'))       // Valor por defecto para futuros registros
				->change();
		});

		// PASO 4: Crear unicidades
		Schema::table('camiones', function (Blueprint $table) {
			// Unicidad por región (para evitar duplicidad)
			$table->unique(['region_operativa_id', 'patente'], 'camiones_region_patente_unique');
		});

		// PASO 5: FK, Laravel le pondrá nombre por defecto ('_foreing') - Igual que en otras tablas
		Schema::table('camiones', function (Blueprint $table) {
			$table->foreign('region_operativa_id')
				->references('id')
				->on('regiones')
				->onDelete('restrict')
				->onUpdate('cascade');
		});
	}

	public function down(): void
	{
		Schema::table('camiones', function (Blueprint $table) {
			// 1. Eliminar unicidades (estas SÍ existen)
			$table->dropUnique('camiones_region_patente_unique');

			// 2. Eliminar FK usando nombre de columna en array (para que Laravel calcule nombre generado automáticamente)
			$table->dropForeign(['region_operativa_id']);

			// 3. Eliminar columna
			$table->dropColumn('region_operativa_id');
		});
	}
};
