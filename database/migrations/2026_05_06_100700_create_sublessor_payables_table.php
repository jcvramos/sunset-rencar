<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sublessor_payables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sublessor_id')->constrained('sublessors')->cascadeOnDelete();
            $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();

            $table->decimal('amount', 12, 2);
            $table->string('currency', 5)->default('HNL');
            $table->enum('status', ['pendiente', 'pagado', 'reversado', 'cancelado'])->default('pendiente');

            $table->date('due_date')->nullable();
            $table->date('paid_at')->nullable();
            $table->string('payment_reference')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['sublessor_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sublessor_payables');
    }
};
