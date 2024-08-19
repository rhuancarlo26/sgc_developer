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
        Schema::create('supervisao_exec_ocorrencia_vistoria', function (Blueprint $table) {
            $table->id();
            $table->string('id_ocorrencia')->nullable();
            $table->string('num_por_ocorrencia')->nullable();
            $table->string('nome_id')->nullable();
            $table->date('data_vistoria')->nullable();
            $table->string('corrigido')->nullable();
            $table->date('data_fim')->nullable();
            $table->string('intensidade_vistoria')->nullable();
            $table->string('tipo_vistoria')->nullable();
            $table->string('acordo_prazo')->nullable();
            $table->string('prazo_vistoria')->nullable();
            $table->string('obs_vistoria')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supervisao_exec_ocorrencia_vistoria');
    }
};
