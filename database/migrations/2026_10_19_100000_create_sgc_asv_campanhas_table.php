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
        // Ambientes que receberam a tabela pelo dump antigo (database/ajustes) já a possuem
        if (Schema::hasTable('sgc_asv_campanhas')) {
            return;
        }

        Schema::create('sgc_asv_campanhas', function (Blueprint $table) {
            $table->id();
            $table->integer('id_contrato');
            $table->string('cod_emp');
            $table->unsignedInteger('id_campanha');
            $table->string('sei_dnit')->nullable();
            $table->string('subproduto');
            $table->string('status', 50)->default('Em elaboração');
            $table->unsignedInteger('versao_analise')->default(1);
            $table->timestamps();

            $table->index('id_contrato', 'idx_sgc_asv_contrato');
            $table->index('cod_emp', 'idx_sgc_asv_cod_emp');
            $table->index('status', 'idx_sgc_asv_status');
            $table->foreign('id_contrato', 'fk_sgc_asv_contrato')
                ->references('id')->on('contratos')
                ->onDelete('no action')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sgc_asv_campanhas');
    }
};
