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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('identity_number')->nullable()->comment('DNI / Pasaporte');
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->string('nationality')->default('HN')->comment('Código ISO del país');
            $table->enum('client_type', ['nacional', 'extranjero', 'corporativo'])->default('nacional');
            $table->enum('status', ['normal', 'vip', 'observacion', 'bloqueado'])->default('normal');
            $table->text('block_reason')->nullable()->comment('Motivo de bloqueo si aplica');
            $table->string('license_photo')->nullable()->comment('Foto de licencia de conducir');
            $table->string('identity_photo')->nullable()->comment('Foto de identidad/pasaporte');
            $table->text('notes')->nullable()->comment('Notas internas del agente');
            $table->string('source')->nullable()->comment('WhatsApp, web, referido, etc.');
            $table->string('city')->nullable();
            $table->string('country')->default('Honduras');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
