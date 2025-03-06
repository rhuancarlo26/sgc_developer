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
        Schema::create('servico_pmqa_campanha_ponto_medicoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campanha_ponto_id')->constrained('servico_pmqa_campanha_pontos')->cascadeOnDelete();
            $table->boolean('sem_coleta')->default(false);
            $table->float('iqa')->nullable();
            $table->text('justificativa')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servico_pmqa_campanha_ponto_medicoes');
    }
};