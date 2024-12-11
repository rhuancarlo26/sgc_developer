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
        Schema::create('resultado_supressao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servico_id')->constrained('servicos');
            $table->string('chave');
            $table->string('periodo', 50);
            $table->dateTime('dt_inicio')->nullable();
            $table->dateTime('dt_final')->nullable();
            $table->text('graf_area_suprimida')->nullable();
            $table->text('analise_supressao_vegetacao')->nullable();
            $table->text('analise_supressao_especies_protegida')->nullable();
            $table->text('analise_pilhas_cadastradas')->nullable();
            $table->text('analise_pilhas_especie_protetigas')->nullable();
            $table->text('analise_destinacao_pilhas')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resultado_supressao');
    }
};
