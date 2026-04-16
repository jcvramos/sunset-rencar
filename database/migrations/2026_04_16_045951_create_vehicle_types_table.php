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
        Schema::create('vehicle_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Pickup, Camioneta 2 filas, Camioneta 3 filas, Turismo, Microbus 11p, Bus 17p');
            $table->string('slug')->unique();
            $table->string('emoji')->nullable()->comment('Emoji para el bot de WhatsApp');
            $table->integer('seats')->default(5);
            $table->text('description')->nullable();
            $table->string('photo_folder')->nullable()->comment('Carpeta NAS con fotos del tipo');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_types');
    }
};
