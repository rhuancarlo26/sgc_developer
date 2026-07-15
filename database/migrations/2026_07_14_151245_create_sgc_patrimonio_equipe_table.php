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
      Schema::create('sgc_patrimonio_equipe', function (Blueprint $table) {
        $table->id();
        $table->string('nome');
        $table->string('cnpj')->nullable();
        $table->string('cpf')->nullable();
        $table->string('email')->nullable();
        $table->string('profissao')->nullable();
        $table->string('carteira_profissional')->nullable();
        $table->text('obs')->nullable();
        $table->string('numero_registro')->nullable();
        $table->string('conselho_classe')->nullable();
        $table->string('ct')->nullable();
        $table->string('funcao')->nullable();
        $table->boolean('ativo')->default(true);
        $table->timestamps();
      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sgc_patrimonio_equipe');
    }
};
