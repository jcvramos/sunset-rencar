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
        Schema::create('vehicle_availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->date('date')->comment('Fecha bloqueada');
            $table->enum('status', ['rentado', 'mantenimiento', 'reservado', 'bloqueado'])->default('rentado');
            $table->unsignedBigInteger('reservation_id')->nullable()->comment('ID de reserva relacionada');
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->unique(['vehicle_id', 'date']);
            $table->index(['vehicle_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_availability');
    }
};
