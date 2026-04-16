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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate')->unique()->comment('Número de placa');
            $table->string('name')->comment('Nombre descriptivo del vehículo');
            $table->foreignId('vehicle_type_id')->constrained('vehicle_types');
            $table->string('brand')->comment('Marca');
            $table->string('model')->comment('Modelo');
            $table->year('year')->comment('Año');
            $table->string('color');
            $table->integer('seats')->default(5);
            $table->enum('status', ['disponible', 'rentado', 'mantenimiento', 'inactivo'])->default('disponible');
            $table->boolean('is_own')->default(true)->comment('true=flota propia, false=subarrendado');
            $table->foreignId('sublessor_id')->nullable()->comment('ID del subarrendador si aplica');
            $table->decimal('daily_rate', 10, 2)->comment('Tarifa diaria base en Lempiras');
            $table->decimal('deposit_amount', 10, 2)->default(0)->comment('Depósito en garantía');
            $table->text('description')->nullable();
            $table->string('main_photo')->nullable()->comment('Foto principal (ruta NAS)');
            $table->json('photos')->nullable()->comment('Galería de fotos (rutas NAS)');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
