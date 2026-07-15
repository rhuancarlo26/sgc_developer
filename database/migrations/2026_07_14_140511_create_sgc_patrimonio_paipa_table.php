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
        Schema::create('sgc_patrimonio_paipa', function (Blueprint $table) {
          $table->id();
          $table->integer('contrato_id');
          $table->foreign('contrato_id')->references('id')->on('contratos');
          $table->string('subproduto');
          $table->integer('empreendimento_id')->nullable();
          $table->foreign('empreendimento_id')->references('id')->on('sgcvw_empreendimentos');
          $table->string('justificativa_sei')->nullable();
          $table->string('justificativa_titulo')->nullable();
          $table->text('justificativa_citacao')->nullable();
          $table->text('justificativa_complementar')->nullable();
          $table->string('status')->default('rascunho');
          $table->integer('versao')->default(1);
          $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sgc_patrimonio_paipa');
    }
};
