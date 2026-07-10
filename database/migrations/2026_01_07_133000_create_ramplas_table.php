<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('ramplas', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('patente', 20);

			// Relación operativa
			$table->unsignedBigInteger('region_operativa_id');

			// Atributos de la rampla
			$table->unsignedBigInteger('tipo_rampla_id');
			$table->unsignedBigInteger('capacidad_rampla_id');
			$table->unsignedBigInteger('estado_rampla_id');

			// Estado lógico
			$table->boolean('activo')->default(true);
			$table->text('observacion_inactividad')->nullable();

			// Auditoría
			$table->timestamp('created_at')->useCurrent();
			$table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

			// Relaciones - Claves foráneas
			$table->foreign('region_operativa_id')->references('id')->on('regiones')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('tipo_rampla_id')->references('id')->on('catalogos')->onUpdate('cascade');
            $table->foreign('capacidad_rampla_id')->references('id')->on('catalogos')->onUpdate('cascade');
            $table->foreign('estado_rampla_id')->references('id')->on('catalogos')->onUpdate('cascade');

			// Unicidad por región
			$table->unique(
				['region_operativa_id', 'patente'],
				'ramplas_region_patente_unique'
			);
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('ramplas');
	}
};
