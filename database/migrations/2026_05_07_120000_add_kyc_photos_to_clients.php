<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('selfie_with_id_photo')->nullable()->after('identity_photo')
                  ->comment('Foto del cliente sosteniendo su identificación (verificación de identidad)');
            $table->string('face_photo')->nullable()->after('selfie_with_id_photo')
                  ->comment('Foto facial sin lentes ni gorra (verificación facial)');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['selfie_with_id_photo', 'face_photo']);
        });
    }
};
