<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('list_parameters')) {
            Schema::drop('list_parameters');
        }
    }

    public function down(): void
    {
        // Estructura heredada irreversible: ApplicationBase usa el catálogo vigente.
    }
};
