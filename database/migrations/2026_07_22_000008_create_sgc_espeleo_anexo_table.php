<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('sgc_espeleo_anexo')) {
            return;
        }

        Schema::create('sgc_espeleo_anexo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_contrato');
            $table->unsignedBigInteger('campanha_id');
            $table->string('tipo', 100)->default('foto');
            $table->string('caminho');
            $table->string('nome');
            $table->text('legenda')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->timestamps();

            $table->index('id_contrato', 'sgc_espeleo_anexo_id_contrato_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sgc_espeleo_anexo');
    }
};
