<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
	public function up(): void
	{
		DB::transaction(function () {

			// Resguardo de IdemPotencia
			if (DB::table('catalogos')->where('id', 71)->exists()) {
				return;
			}

			// 1️⃣ Ordenar Coordinador XII : Desplazar en uno todos los roles con orden entre [ 7, 9 ] e id < 70
			DB::table('catalogos')
				->where('catalogo_id', 1)
				->whereBetween('orden', [7, 9])
				->where('id', '!=', 70)
				->increment('orden');

			// 2️⃣ Hacer espacio para nuevo Solicitanet Planta XII - Desplazar todos los roles con orden >= 2
			DB::table('catalogos')
				->where('catalogo_id', 1)
				->where('orden', '>=', 2)
				->increment('orden');

			// 3️⃣ Insertar nuevo rol
			DB::table('catalogos')->insert([
				'id'          => 71,
				'catalogo_id' => 1,
				'nombre'      => 'Solicitante Planta XII',
				'orden'       => 2,
				'activo'      => 1,
				'created_at'  => now(),
				'updated_at'  => now(),
			]);
		});
	}

	public function down(): void
	{
		DB::transaction(function () {

			// Resguardo de IdemPotencia
			if (!DB::table('catalogos')->where('id', 71)->exists()) {
				return;
			}

			// 1️⃣ Eliminar nuevo rol
			DB::table('catalogos')
				->where('id', 71)
				->delete();

			// 2️⃣ Revertir desplazamiento orden >= 2
			DB::table('catalogos')
				->where('catalogo_id', 1)
				->where('orden', '>=', 3)
				->decrement('orden');

			// 3️⃣ Revertir bloque 7–9 original
			DB::table('catalogos')
				->where('catalogo_id', 1)
				->whereBetween('orden', [8, 10])
				->where('id', '!=', 70)
				->decrement('orden');
		});
	}
};
