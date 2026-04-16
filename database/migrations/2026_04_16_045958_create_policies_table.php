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
        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('Identificador único de la política');
            $table->string('group')->comment('destinos, cancelacion, deposito, documentos, tarifas');
            $table->string('label')->comment('Nombre legible');
            $table->json('value')->comment('Valor de la política en formato JSON');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabla de destinos autorizados/restringidos
        Schema::create('policy_destinations', function (Blueprint $table) {
            $table->id();
            $table->string('municipality');
            $table->string('department')->nullable();
            $table->enum('status', ['autorizado', 'restringido', 'bloqueado'])->default('autorizado');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabla de penalidades por cancelación
        Schema::create('policy_cancellations', function (Blueprint $table) {
            $table->id();
            $table->integer('hours_before')->comment('Horas de anticipación mínima');
            $table->integer('hours_before_max')->nullable()->comment('Rango máximo de horas');
            $table->decimal('refund_percentage', 5, 2)->comment('Porcentaje de devolución 0-100');
            $table->string('label')->comment('Descripción: ej. +48h = 100% devolución');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policy_cancellations');
        Schema::dropIfExists('policy_destinations');
        Schema::dropIfExists('policies');
    }
};
