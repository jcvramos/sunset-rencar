<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('damages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->nullable()->constrained('reservations')->nullOnDelete();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();

            $table->enum('zone', [
                'frontal',
                'trasera',
                'lateral_izq',
                'lateral_der',
                'interior_frontal',
                'interior_trasero',
                'tablero',
                'odometro',
                'mecanico',
                'otro',
            ]);
            $table->text('description');
            $table->string('evidence_photo')->nullable();
            $table->json('extra_photos')->nullable();

            $table->decimal('amount_charged', 12, 2)->default(0);
            $table->boolean('linked_to_deposit')->default(false);
            $table->enum('status', ['pendiente', 'cobrado', 'disputado', 'absorbido'])->default('pendiente');

            $table->date('occurred_at')->nullable();
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reported_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['vehicle_id', 'status']);
            $table->index(['client_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damages');
    }
};
