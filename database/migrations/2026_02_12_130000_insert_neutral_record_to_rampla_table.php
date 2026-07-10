<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {

            // Si ya existe, no hacemos nada (idempotencia real)
            if (DB::table('ramplas')->where('id', 0)->exists()) {
                return;
            }

            // ⚠️ Necesario para permitir insertar id=0 en AUTO_INCREMENT
            DB::statement('SET SESSION sql_mode = REPLACE(@@sql_mode, "NO_AUTO_VALUE_ON_ZERO", "")');
            DB::statement('SET SESSION sql_mode = CONCAT(@@sql_mode, ",NO_AUTO_VALUE_ON_ZERO")');

            DB::table('ramplas')->insert([
                'id'                      => 0,
                'patente'                 => 'NO ESPECIFICADO',
                'region_operativa_id'     => config('constantes.REGION_X'), // o la que uses como neutra
                'tipo_rampla_id'          => config('constantes.CATALOGO_NO_ESPECIFICADO'),
                'capacidad_rampla_id'     => config('constantes.CATALOGO_NO_ESPECIFICADO'),
                'estado_rampla_id'        => config('constantes.CATALOGO_NO_ESPECIFICADO'),
                'activo'                  => 1,
                'observacion_inactividad' => null,
                'created_at'              => now(),
                'updated_at'              => now(),
            ]);
        });
    }

    public function down(): void
    {
        DB::table('ramplas')->where('id', 0)->delete();
    }
};
