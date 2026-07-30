<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('sgc_espeleo_campanhas')) {
            return;
        }

        Schema::create('sgc_espeleo_campanhas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_contrato')->nullable();
            $table->string('id_campanha', 50)->nullable();
            $table->enum('status', ['Em elaboração', 'Em análise', 'Aprovada', 'Reprovada'])->default('Em elaboração');
            $table->integer('versao_analise')->default(1);
            $table->string('cod_emp', 50)->nullable();
            $table->string('subproduto')->nullable();
            $table->string('subtrecho')->nullable()->comment('Concatenação de subtrecho_ini e subtrecho_fim');
            $table->string('segmento')->nullable()->comment('Concatenação de km_ini e km_fim');
            $table->decimal('extensao', 10, 2)->nullable()->comment('Diferença entre km_fim e km_ini');
            $table->string('tipo_de_intervencao')->nullable();
            $table->text('descricao')->nullable()->comment('Descrição da adequação de capacidade');
            $table->string('bioma')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sgc_espeleo_campanhas');
    }
};
