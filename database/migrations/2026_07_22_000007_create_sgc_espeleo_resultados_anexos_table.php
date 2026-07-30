<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('sgc_espeleo_resultados_anexos')) {
            return;
        }

        Schema::create('sgc_espeleo_resultados_anexos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_contrato');
            $table->unsignedBigInteger('campanha_id');
            $table->string('nome_arquivo')->nullable();
            $table->enum('tipo_anexo', [
                'anuencia_proprietarios',
                'registro_fotografico',
                'dados_secundarios',
                'art',
                'ret',
                'cr',
                'ctf',
                'anuencia_colecoes',
                'oficio_atividades_campo',
            ])->nullable();
            $table->string('nome')->nullable();
            $table->string('caminho');
            $table->string('tipo', 100)->nullable();
            $table->text('comentario')->nullable();
            $table->integer('versao')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sgc_espeleo_resultados_anexos');
    }
};
