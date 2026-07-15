<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('catalogos')) {
            return;
        }

        $catalogoPorId = DB::table('catalogos')->where('id', 1)->first();

        if ($catalogoPorId) {
            if ($catalogoPorId->catalogo_id === null && $catalogoPorId->nombre === 'Rol de Usuario') {
                return;
            }

            throw new \RuntimeException('El catálogo ID 1 existe, pero no corresponde a Rol de Usuario.');
        }

        $catalogoPorNombre = DB::table('catalogos')
            ->whereNull('catalogo_id')
            ->where('nombre', 'Rol de Usuario')
            ->first();

        if ($catalogoPorNombre) {
            throw new \RuntimeException('Rol de Usuario existe con un ID distinto del contrato histórico esperado.');
        }

        DB::table('catalogos')->insert([
            'id' => 1,
            'catalogo_id' => null,
            'nombre' => 'Rol de Usuario',
            'orden' => 1,
            'activo' => true,
            'updated_at' => now(),
            'created_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Reversión conservadora: no es posible distinguir posteriormente si la
        // categoría fue creada aquí o ya era un dato compartido con consumidores.
    }
};
