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
        Schema::create('servicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_id')->constrained('contratos')->cascadeOnDelete();
            $table->foreignId('servico_tema_id')->constrained('servico_temas')->cascadeOnDelete();
            $table->foreignId('servico_tipo_id')->constrained('servico_tipos')->cascadeOnDelete();
            $table->foreignId('servico_status_id')->default(1)->constrained('servico_status')->cascadeOnDelete();
            $table->text('especificacao');
            $table->text('introducao');
            $table->text('justificativa');
            $table->text('objetivo');
            $table->text('metodologia');
            $table->text('publico_alvo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicos');
    }
};