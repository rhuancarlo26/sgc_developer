<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Entrega simplificada dos produtos (planilha + fotos/anexos + análise do fiscal).
     * Ambientes que receberam as tabelas pelo dump antigo (database/ajustes) já as possuem.
     */
    public function up(): void
    {
        if (!Schema::hasTable('sgc_entregas_simplificadas')) {
            Schema::create('sgc_entregas_simplificadas', function (Blueprint $table) {
                $table->id();
                $table->integer('id_contrato');
                $table->string('produto_tipo', 30);
                $table->string('entidade_tipo', 100);
                $table->unsignedBigInteger('entidade_id');
                $table->unsignedBigInteger('modelo_id')->nullable();
                $table->string('planilha_nome');
                $table->string('planilha_caminho', 500);
                $table->enum('status', ['Em elaboração', 'Em análise', 'Aprovada', 'Rejeitada', 'Arquivada'])->default('Em elaboração');
                $table->unsignedInteger('versao_analise')->default(1);
                $table->integer('aprovado_por')->nullable();
                $table->dateTime('data_aprovacao')->nullable();
                $table->dateTime('arquivada_em')->nullable();
                $table->timestamps();

                $table->unique(['entidade_tipo', 'entidade_id'], 'uk_entrega_simplificada_origem');
                $table->index('id_contrato', 'idx_entrega_simplificada_contrato');
                $table->index('produto_tipo', 'idx_entrega_simplificada_produto');
                $table->index('status', 'idx_entrega_simplificada_status');
                $table->index('modelo_id', 'idx_entrega_simplificada_modelo');
                $table->index('aprovado_por', 'idx_entrega_simplificada_aprovador');
            });
        }

        if (!Schema::hasTable('sgc_entrega_simplificada_analises')) {
            Schema::create('sgc_entrega_simplificada_analises', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('entrega_simplificada_id');
                $table->unsignedInteger('versao_analise');
                $table->enum('status', ['Aprovada', 'Rejeitada']);
                $table->text('observacoes')->nullable();
                $table->integer('fiscal_id')->nullable();
                $table->timestamps();

                $table->unique(['entrega_simplificada_id', 'versao_analise'], 'uk_entrega_analise_versao');
                $table->index('fiscal_id', 'idx_entrega_analise_fiscal');
                $table->index('status', 'idx_entrega_analise_status');
                $table->foreign('entrega_simplificada_id', 'fk_entrega_simplificada_analise_entrega')
                    ->references('id')->on('sgc_entregas_simplificadas')
                    ->cascadeOnDelete()->cascadeOnUpdate();
            });
        }

        if (!Schema::hasTable('sgc_entrega_simplificada_arquivos')) {
            Schema::create('sgc_entrega_simplificada_arquivos', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('entrega_simplificada_id');
                $table->enum('tipo', ['foto', 'anexo']);
                $table->string('nome_arquivo');
                $table->string('caminho_arquivo', 500);
                $table->string('mime_type', 150)->nullable();
                $table->unsignedBigInteger('tamanho_bytes')->nullable();
                $table->text('descricao')->nullable();
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
                $table->dateTime('data_captura')->nullable();
                $table->json('metadados')->nullable();
                $table->timestamps();

                $table->index('entrega_simplificada_id', 'idx_entrega_arquivo_entrega');
                $table->index('tipo', 'idx_entrega_arquivo_tipo');
                $table->foreign('entrega_simplificada_id', 'fk_entrega_simplificada_arquivo_entrega')
                    ->references('id')->on('sgc_entregas_simplificadas')
                    ->cascadeOnDelete()->cascadeOnUpdate();
            });
        }

        if (!Schema::hasTable('sgc_fauna_entrega_simplificada_planilhas')) {
            Schema::create('sgc_fauna_entrega_simplificada_planilhas', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('entrega_simplificada_id');
                $table->enum('tipo', ['terrestre', 'aquatica', 'cavernicola']);
                $table->unsignedBigInteger('modulo_id');
                $table->string('nome_arquivo');
                $table->string('caminho_arquivo', 500);
                $table->string('mime_type', 100)->nullable();
                $table->unsignedBigInteger('tamanho_bytes')->nullable();
                $table->timestamps();

                $table->unique(['entrega_simplificada_id', 'tipo'], 'uk_fauna_entrega_simplificada_planilha_tipo');
                $table->index('modulo_id', 'idx_fauna_entrega_simplificada_planilha_modulo');
                $table->foreign('entrega_simplificada_id', 'fk_fauna_entrega_simplificada_planilha_entrega')
                    ->references('id')->on('sgc_entregas_simplificadas')
                    ->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreign('modulo_id', 'fk_fauna_entrega_simplificada_planilha_modulo')
                    ->references('id')->on('sgc_modulos')
                    ->cascadeOnUpdate();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sgc_fauna_entrega_simplificada_planilhas');
        Schema::dropIfExists('sgc_entrega_simplificada_arquivos');
        Schema::dropIfExists('sgc_entrega_simplificada_analises');
        Schema::dropIfExists('sgc_entregas_simplificadas');
    }
};
