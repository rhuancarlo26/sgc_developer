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
        Schema::create('servico_pmqa_resultado_analise_iqas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resultado_id')->constrained('servico_pmqa_resultados')->cascadeOnDelete();
            $table->string('analise');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servico_pmqa_resultado_analise_iqas');
    }
};