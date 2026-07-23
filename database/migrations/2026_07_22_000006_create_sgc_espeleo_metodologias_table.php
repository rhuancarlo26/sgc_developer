<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('sgc_espeleo_metodologias')) {
            return;
        }

        Schema::create('sgc_espeleo_metodologias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campanha_id');
            $table->string('id_modulo', 45)->nullable();
            $table->integer('id_contrato')->nullable();
            $table->string('grupo_faunistico')->nullable();
            $table->text('metodologia')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sgc_espeleo_metodologias');
    }
};
