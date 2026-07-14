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
        Schema::table('retiros', function (Blueprint $table) {
			$table->unsignedTinyInteger('tipo_operacion')
				->default(0)					// 0 = TIPO_OPERACION_RETIRO
				->comment('0=retiro, 1=reposicion')
				->after('requiere_reposicion'); // ajusta posición si es necesario

			$table->index('tipo_operacion', 'idx_retiros_tipo_operacion');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
		Schema::table('retiros', function (Blueprint $table) {
			$table->dropIndex('idx_retiros_tipo_operacion');
			$table->dropColumn('tipo_operacion');
		});

    }
};
