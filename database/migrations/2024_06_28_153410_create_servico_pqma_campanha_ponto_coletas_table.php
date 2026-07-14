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
        Schema::create('servico_pqma_campanha_ponto_coletas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campanha_ponto_id')->constrained('servico_pmqa_campanha_pontos')->cascadeOnDelete();
            $table->date('data_coleta');
            $table->boolean('sem_coleta');
            $table->string('numero_amostra')->nullable();
            $table->string('preservacao_amostra')->nullable();
            $table->string('acondicionamento_amostra')->nullable();
            $table->string('transporte_amostra')->nullable();
            $table->string('justificativa')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servico_pqma_campanha_ponto_coletas');
    }
};