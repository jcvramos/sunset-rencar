<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservation_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->string('stage', 30);
            $table->enum('status', ['pendiente', 'completada', 'omitida'])->default('pendiente');
            $table->json('data')->nullable()->comment('Datos específicos de la etapa');
            $table->text('notes')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['reservation_id', 'stage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_stages');
    }
};
