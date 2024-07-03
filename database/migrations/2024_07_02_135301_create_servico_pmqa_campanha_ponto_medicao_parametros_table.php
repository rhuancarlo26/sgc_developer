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
        Schema::create('servico_pmqa_camp_p_med_parametros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ponto_medicao_id')->constrained('servico_pmqa_campanha_ponto_medicoes')->cascadeOnDelete();
            $table->foreignId('lista_parametro_id')->constrained('servico_pmqa_lista_parametros')->cascadeOnDelete();
            $table->float('medicao');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servico_pmqa_camp_p_med_parametros');
    }
};