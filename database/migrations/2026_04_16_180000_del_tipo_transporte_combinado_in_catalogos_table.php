<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Elimina el registro SOLO si existe
        DB::table('catalogos')
            ->where('id', 195)
            ->delete();
    }

    public function down(): void
    {
        // Reinsertar solo si NO existe (rollback seguro)
        $existe = DB::table('catalogos')
            ->where('id', 195)
            ->exists();

        if (!$existe) {
            DB::table('catalogos')->insert([
                'id'           => 195,
                'catalogo_id'  => 140, // Tipo Transporte
                'nombre'       => 'Combinado',
                'orden'        => 3,
                'activo'       => 1,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }
};
