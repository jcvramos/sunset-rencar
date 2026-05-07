<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservation_extensions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->date('previous_end_date');
            $table->date('new_end_date');
            $table->unsignedInteger('additional_days');
            $table->decimal('additional_amount', 12, 2);
            $table->text('reason')->nullable();
            $table->string('document_path')->nullable()->comment('PDF de extensión firmado');
            $table->string('signature_path')->nullable()->comment('Firma digital cliente');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_extensions');
    }
};
