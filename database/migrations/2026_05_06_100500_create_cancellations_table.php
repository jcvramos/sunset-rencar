<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cancellations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->foreignId('policy_cancellation_id')->nullable()->constrained('policy_cancellations')->nullOnDelete();
            $table->text('reason');
            $table->decimal('hours_anticipation', 8, 2)->default(0);
            $table->decimal('refund_percentage', 5, 2)->default(0);
            $table->decimal('total_paid', 12, 2)->default(0);
            $table->decimal('amount_refunded', 12, 2)->default(0);
            $table->decimal('amount_retained', 12, 2)->default(0);
            $table->string('document_path')->nullable()->comment('PDF de cancelación');
            $table->string('signature_path')->nullable()->comment('Firma digital cliente');
            $table->enum('status', ['pendiente_firma', 'firmado', 'reembolsado', 'cerrado'])->default('pendiente_firma');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cancellations');
    }
};
