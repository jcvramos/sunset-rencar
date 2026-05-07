<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sublessors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('identity_number')->nullable()->comment('DNI / RTN');
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->enum('payment_method', ['transferencia', 'efectivo', 'cheque'])->default('transferencia');
            $table->decimal('default_share', 5, 2)->default(0)->comment('% sobre tarifa que recibe el subarrendador');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // FK pendiente desde vehicles.sublessor_id
        Schema::table('vehicles', function (Blueprint $table) {
            $table->foreign('sublessor_id')->references('id')->on('sublessors')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['sublessor_id']);
        });
        Schema::dropIfExists('sublessors');
    }
};
