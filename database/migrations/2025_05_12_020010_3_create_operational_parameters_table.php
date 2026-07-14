<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operational_parameters', function (Blueprint $table) {
            $table->bigIncrements('id')->nullable(false);
            
            // Email de contacto de soporte IT
            $table->string('support_email', 255)->nullable();
    
            // Casilla donde se enviará copia de todos los correos para auditoría
            $table->string('audit_email', 255)->nullable();
    
            // Indica si está activa la funcionalidad de envío de copia de correos
            $table->boolean('audit_email_enabled')->default(false);
    
            // Indica si los usuarios pueden modificar sus datos de perfil
            $table->boolean('allow_profile_editing')->default(true);

            // Indica la velocidad promedio de los camiones
            $table->integer('average_truck_speed')->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operational_parameters');
    }
};
