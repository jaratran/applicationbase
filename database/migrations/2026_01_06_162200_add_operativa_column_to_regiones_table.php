<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::table('regiones', function (Blueprint $table) {
			$table->boolean('operativa')
				->default(false)
				->after('orden');
		});
	}

	public function down(): void
	{
		Schema::table('regiones', function (Blueprint $table) {
			$table->dropColumn('operativa');
		});
	}
};
