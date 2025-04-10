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
        Schema::create('controle_pilhas', function (Blueprint $table) {
            $table->id();
            $table->string('chave');
            $table->foreignId('servico_id')->constrained('servicos');
            $table->foreignId('licenca_id')->constrained('licenca');
            $table->foreignId('area_supressao_id')->constrained('area_supressao');
            $table->foreignId('patio_estocagem_id')->constrained('patio_estocagem');
            $table->foreignId('corte_especie_id')->nullable()->constrained('corte_especie');
            $table->foreignId('tipo_produto_id')->constrained('tipo_produto');
            $table->dateTime('dt_cadastro')->nullable();
            $table->tinyInteger('tipo_pilha');
            $table->decimal('volume');
            $table->integer('nu_individuo');
            $table->string('latitude');
            $table->string('longitude');
            $table->string('zona')->nullable();
            $table->text('observacao')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('controle_pilhas');
    }
};
