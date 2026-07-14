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
        Schema::create('recurso_rh', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_id')->constrained('contratos', 'id')->cascadeOnDelete();
            $table->string('nome');
            $table->string('telefone');
            $table->string('cpf');
            $table->string('email');
            $table->string('formacao');
            $table->string('funcao');
            $table->string('ctf');
            $table->string('ctf_validade');
            $table->boolean('conselho_classe');
            $table->string('numero_registro')->nullable();
            $table->string('observacao');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurso_rh');
    }
};