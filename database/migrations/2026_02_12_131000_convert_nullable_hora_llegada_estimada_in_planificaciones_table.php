<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE planificaciones
            MODIFY hora_llegada_estimada DATETIME NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE planificaciones
            MODIFY hora_llegada_estimada DATETIME NOT NULL
        ");
    }
};
