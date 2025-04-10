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
        Schema::create('supervisao_exec_ocorrencia', function (Blueprint $table) {
            $table->id();
            $table->string('id_servico');
            $table->string('num_por_servico');
            $table->string('nome_id');
            $table->string('id_rodovia');
            $table->string('id_uf');
            $table->date('data_ocorrencia');
            $table->string('km');
            $table->string('estaca');
            $table->string('lado');
            $table->string('latitude');
            $table->string('longitude');
            $table->string('zona');
            $table->string('id_lote');
            $table->string('indicio_responsabilidade');
            $table->string('possivel_causa');
            $table->string('descricao_causa');
            $table->string('intensidade');
            $table->string('tipo');
            $table->string('rnc_direto');
            $table->string('local');
            $table->string('classificacao');
            $table->string('descricao');
            $table->string('area_total');
            $table->string('prazo');
            $table->string('dias_restantes');
            $table->string('acoes');
            $table->string('norma');
            $table->string('obs');
            $table->string('status');
            $table->string('aprovado_rnc');
            $table->string('parecer_fiscal');
            $table->string('envio_empresa');
            $table->string('envio_fiscal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supervisao_exec_ocorrencia');
    }
};
