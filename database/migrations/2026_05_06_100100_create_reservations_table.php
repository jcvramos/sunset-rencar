<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique()->comment('Folio: RES-2026-00001');

            $table->foreignId('client_id')->constrained('clients');
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->foreignId('vehicle_type_id')->nullable()->constrained('vehicle_types')->nullOnDelete();
            $table->foreignId('sublessor_id')->nullable()->constrained('sublessors')->nullOnDelete();

            // Fechas y montos
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('days')->default(1);
            $table->decimal('daily_rate', 10, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->enum('deposit_status', ['pendiente', 'pagado', 'aplicado', 'devuelto'])->default('pendiente');
            $table->date('deposit_paid_at')->nullable();

            $table->string('destination')->nullable();
            $table->string('destination_municipality')->nullable();
            $table->text('notes')->nullable();

            // Etapa actual del flujo de 9 etapas
            $table->enum('current_stage', [
                'cotizacion',     // 1
                'deposito',       // 2
                'validacion',     // 3
                'confirmacion',   // 4
                'entrega',        // 5
                'activa',         // 6 — renta en curso
                'extension',      // 7 — opcional
                'recepcion',      // 8
                'factura',        // 9
                'cerrada',
                'cancelada',
            ])->default('cotizacion');

            $table->boolean('requires_approval')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('source')->default('manual')->comment('manual|whatsapp|bot|web');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['current_stage', 'start_date']);
            $table->index(['vehicle_id', 'start_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
