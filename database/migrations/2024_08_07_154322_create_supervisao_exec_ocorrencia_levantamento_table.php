<?php

use App\Models\ServicoConOcorrSupervisaoExecOcorrenciaLevantamento;
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
        Schema::create('supervisao_exec_ocorrencia_levantamento', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->timestamps();
        });

        $levantamentos = [
            [
                "id" => 1,
                "nome" => "Ocorrência registrada"
            ],
            [
                "id" => 2,
                "nome" => "Ocorrência atualizada"
            ],
            [
                "id" => 3,
                "nome" => "Vistoria realizada"
            ],
            [
                "id" => 4,
                "nome" => "Ocorrência enviada a construtora e supervisora"
            ],
            [
                "id" => 5,
                "nome" => "RNC enviada ao fiscal"
            ],
            [
                "id" => 6,
                "nome" => "RNC aprovada pelo fiscal"
            ],
            [
                "id" => 7,
                "nome" => "RNC reprovada pelo fiscal"
            ],
            [
                "id" => 8,
                "nome" => "RNC enviada a construtora e supervisora"
            ],
            [
                "id" => 9,
                "nome" => "Ocorrência corrigida"
            ]
        ];

        ServicoConOcorrSupervisaoExecOcorrenciaLevantamento::insert($levantamentos);
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supervisao_exec_ocorrencia_levantamento');
    }
};
