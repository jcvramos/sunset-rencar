<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type', 60)->comment('reserva_creada, deposito_pagado, entrega, recepcion, cancelacion, dano, etc.');
            $table->morphs('recipient');
            $table->morphs('source');
            $table->string('channel', 20)->default('in_app')->comment('in_app|whatsapp|email|sms');
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable();
            $table->enum('status', ['pendiente', 'enviada', 'leida', 'fallida'])->default('pendiente');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_notifications');
    }
};
