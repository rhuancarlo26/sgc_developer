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
        Schema::create('supervisao_config_lotes', function (Blueprint $table) {
            $table->id();
            $table->string('id_servico')->nullable();
            $table->string('num_por_servico')->nullable();
            $table->string('nome_id')->nullable();
            $table->string('nome')->nullable();
            $table->string('id_rodovia')->nullable();
            $table->string('id_uf')->nullable();
            $table->string('km_inicial')->nullable();
            $table->string('km_final')->nullable();
            $table->string('estaca_inicial')->nullable();
            $table->string('estaca_final')->nullable();
            $table->string('empresa')->nullable();
            $table->string('num_contrato')->nullable();
            $table->string('obj_contrato')->nullable();
            $table->string('contato_empresa')->nullable();
            $table->string('email_empresa')->nullable();
            $table->string('fiscal_contrato')->nullable();
            $table->string('email_fiscal')->nullable();
            $table->string('situacao_contrato')->nullable();
            $table->string('supervisora_obras')->nullable();
            $table->string('num_contrato_supervisora')->nullable();
            $table->string('obj_contrato_supervisora')->nullable();
            $table->string('contato_supervisora')->nullable();
            $table->string('email_supervisora')->nullable();
            $table->string('fiscal_contrato_supervisora')->nullable();
            $table->string('email_fiscal_supervisora')->nullable();
            $table->string('situacao_contrato_supervisora')->nullable();
            $table->string('situacao_contato_supervisora')->nullable();
            $table->string('obs')->nullable();
            $table->string('lat_inicial')->nullable();
            $table->string('lat_final')->nullable();
            $table->string('long_inicial')->nullable();
            $table->string('long_final')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supervisao_config_lotes');
    }
};