<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabelas de campanha dos produtos com entrega simplificada.
     * Estrutura espelhada de sgc_asv_campanhas (dump em database/ajustes/asv_campanhas.sql).
     */
    private array $tabelas = [
        'indigena'   => 'sgc_indigena_campanhas',
        'quilombola' => 'sgc_quilombola_campanhas',
        'audiencia'  => 'sgc_audiencia_campanhas',
        'pba'        => 'sgc_pba_campanhas',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tabelas as $prefixo => $tabela) {
            if (Schema::hasTable($tabela)) {
                continue;
            }

            Schema::create($tabela, function (Blueprint $table) use ($prefixo) {
                $table->id();
                $table->integer('id_contrato');
                $table->string('cod_emp');
                $table->unsignedInteger('id_campanha');
                $table->string('sei_dnit')->nullable();
                $table->string('subproduto');
                $table->string('status', 50)->default('Em elaboração');
                $table->unsignedInteger('versao_analise')->default(1);
                $table->timestamps();

                $table->index('id_contrato', "idx_sgc_{$prefixo}_contrato");
                $table->index('cod_emp', "idx_sgc_{$prefixo}_cod_emp");
                $table->index('status', "idx_sgc_{$prefixo}_status");
                $table->foreign('id_contrato', "fk_sgc_{$prefixo}_contrato")
                    ->references('id')->on('contratos')
                    ->onDelete('no action')->onUpdate('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tabelas as $tabela) {
            Schema::dropIfExists($tabela);
        }
    }
};
