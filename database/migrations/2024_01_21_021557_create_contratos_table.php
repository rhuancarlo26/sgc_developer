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
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_tipo_id')->constrained('contrato_tipos', 'id')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->string('numero_contrato', 13);
            $table->string('cnpj', 18);
            $table->string('contratada');
            $table->string('processo_sei', 20);
            $table->text('objeto');
            $table->date('data_inicio_vigencia');
            $table->date('data_termino_vigencia');
            $table->string('situacao');
            $table->string('edital', 20);
            $table->string('tipo_licitacao', 20);
            $table->string('modalidade');
            $table->string('unidade_gestora');
            $table->string('fiscal_contrato');
            $table->string('snv');
            $table->float('preco_inicial');
            $table->float('total_aditivo');
            $table->float('total_reajuste');
            $table->float('total');
            $table->text('introducao')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};