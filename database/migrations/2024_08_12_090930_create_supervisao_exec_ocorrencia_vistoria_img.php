<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('supervisao_exec_ocorrencia_vistoria_img', function (Blueprint $table) {
            $table->id();
            $table->string('id_vistoria');
            $table->string('nome');
            $table->string('caminho_arquivo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supervisao_exec_ocorrencia_vistoria_img');
    }
};
