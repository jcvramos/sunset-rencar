<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservation_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->enum('type', ['entrega', 'recepcion'])->comment('Etapa de captura');
            $table->enum('zone', [
                'frontal',
                'trasera',
                'lateral_izq',
                'lateral_der',
                'interior_frontal',
                'interior_trasero',
                'tablero',
                'odometro',
            ])->comment('Una de las 8 zonas obligatorias');
            $table->string('path')->comment('Ruta NAS / storage');
            $table->string('original_name')->nullable();
            $table->string('mime_type', 50)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('captured_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('captured_at')->nullable();
            $table->timestamps();

            $table->unique(['reservation_id', 'type', 'zone']);
            $table->index(['reservation_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_photos');
    }
};
