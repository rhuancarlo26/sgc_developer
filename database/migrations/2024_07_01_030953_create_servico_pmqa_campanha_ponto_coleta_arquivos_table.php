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
        Schema::create('servico_pmqa_campanha_ponto_coleta_arquivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coleta_id')->constrained('servico_pqma_campanha_ponto_coletas')->cascadeOnDelete();
            $table->string('nome');
            $table->string('caminho');
            $table->string('tipo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servico_pmqa_campanha_ponto_coleta_arquivos');
    }
};