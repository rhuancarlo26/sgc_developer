<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Módulo de gestão de prazos de licenças e autorizações ambientais (sgc_licencas).
     * Independente do domínio legado Licenca (tabelas licencas/condicionantes).
     */
    public function up(): void
    {
        Schema::create('sgc_licencas_tipos', function (Blueprint $table) {
            $table->id();
            $table->string('sigla', 20);
            $table->string('nome');
            // Autorização de Operação do licenciamento corretivo (PI nº 1/2020): exibida no módulo de Regularização
            $table->boolean('regularizacao')->default(false);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        Schema::create('sgc_licencas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_id')->constrained('sgc_licencas_tipos');
            $table->string('numero', 100);
            $table->string('orgao_emissor', 100);
            $table->integer('uf_id')->nullable();
            $table->string('empreendimento')->nullable();
            $table->string('processo_sei', 50)->nullable();
            $table->string('sei_documento', 50)->nullable();
            $table->date('data_emissao');
            $table->date('data_validade')->nullable();
            $table->unsignedSmallInteger('antecedencia_renovacao_dias')->nullable();
            $table->foreignId('licenca_anterior_id')->nullable()->constrained('sgc_licencas')->nullOnDelete();
            $table->string('motivo_vinculo', 20)->nullable();
            $table->integer('contrato_id')->nullable();
            $table->string('status', 20)->default('ativa');
            $table->text('observacoes')->nullable();
            $table->string('arquivo_nome')->nullable();
            $table->string('arquivo_caminho', 500)->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('uf_id', 'idx_sgc_licencas_uf');
            $table->index('status', 'idx_sgc_licencas_status');
            $table->index('data_validade', 'idx_sgc_licencas_validade');
            $table->foreign('uf_id', 'fk_sgc_licencas_uf')->references('id')->on('estados');
            $table->foreign('contrato_id', 'fk_sgc_licencas_contrato')->references('id')->on('contratos')->nullOnDelete();
            $table->foreign('created_by', 'fk_sgc_licencas_user')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('sgc_licencas_responsaveis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('licenca_id')->constrained('sgc_licencas')->cascadeOnDelete();
            $table->integer('user_id');
            $table->timestamps();

            $table->unique(['licenca_id', 'user_id'], 'uk_sgc_licencas_responsavel');
            $table->foreign('user_id', 'fk_sgc_licencas_responsavel_user')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('sgc_licencas_condicionantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('licenca_id')->constrained('sgc_licencas')->cascadeOnDelete();
            $table->string('numero', 20);
            $table->string('grupo', 20)->nullable();
            $table->string('titulo')->nullable();
            $table->text('descricao');
            $table->string('tipo_prazo', 30)->default('sem_prazo');
            $table->unsignedSmallInteger('prazo_dias')->nullable();
            $table->date('data_fixa')->nullable();
            $table->unsignedTinyInteger('dia_recorrencia')->nullable();
            $table->unsignedTinyInteger('mes_recorrencia')->nullable();
            $table->string('relatorio_sigla', 20)->nullable();
            $table->boolean('periodo_ano_anterior')->default(false);
            $table->string('situacao', 30)->default('em_atendimento');
            $table->text('situacao_descricao')->nullable();
            $table->unsignedInteger('ordem')->default(0);
            $table->timestamps();

            $table->index('relatorio_sigla', 'idx_sgc_licencas_cond_relatorio');
        });

        Schema::create('sgc_licencas_prazos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('licenca_id')->constrained('sgc_licencas')->cascadeOnDelete();
            $table->foreignId('condicionante_id')->nullable()->constrained('sgc_licencas_condicionantes')->cascadeOnDelete();
            $table->string('origem', 20);
            $table->string('referencia', 40);
            $table->string('descricao');
            $table->unsignedSmallInteger('ano_referencia')->nullable();
            $table->date('periodo_inicio')->nullable();
            $table->date('periodo_fim')->nullable();
            $table->date('data_evento')->nullable();
            $table->date('data_limite_original');
            $table->date('data_limite');
            $table->string('status', 20)->default('pendente');
            $table->date('data_cumprimento')->nullable();
            $table->timestamps();

            $table->index(['licenca_id', 'origem', 'condicionante_id', 'referencia'], 'idx_sgc_licencas_prazo_chave');
            $table->index(['status', 'data_limite'], 'idx_sgc_licencas_prazo_status');
        });

        Schema::create('sgc_licencas_prazo_registros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prazo_id')->constrained('sgc_licencas_prazos')->cascadeOnDelete();
            $table->string('tipo', 20);
            $table->date('data');
            $table->string('numero_oficio', 150)->nullable();
            $table->string('numero_sei', 50)->nullable();
            $table->text('descricao')->nullable();
            $table->date('data_limite_anterior')->nullable();
            $table->date('data_limite_nova')->nullable();
            $table->string('arquivo_nome')->nullable();
            $table->string('arquivo_caminho', 500)->nullable();
            $table->integer('user_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id', 'fk_sgc_licencas_registro_user')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('sgc_licencas_alertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prazo_id')->constrained('sgc_licencas_prazos')->cascadeOnDelete();
            $table->string('marco', 20);
            $table->timestamp('enviado_em');

            $table->unique(['prazo_id', 'marco'], 'uk_sgc_licencas_alerta');
        });

        $agora = now();
        DB::table('sgc_licencas_tipos')->insert(array_map(fn ($tipo) => [
            'sigla' => $tipo[0], 'nome' => $tipo[1], 'regularizacao' => $tipo[2] ?? false,
            'ativo' => true, 'created_at' => $agora, 'updated_at' => $agora,
        ], [
            ['AO', 'Autorização de Operação', true],
            ['LP', 'Licença Prévia'],
            ['LI', 'Licença de Instalação'],
            ['LO', 'Licença de Operação'],
            ['LIO', 'Licença de Instalação e Operação'],
            ['LAR', 'Licença Ambiental de Regularização'],
            ['LAU', 'Licença Ambiental Única'],
            ['LS', 'Licença Simplificada'],
            ['AA', 'Autorização Ambiental'],
            ['ASV', 'Autorização de Supressão de Vegetação'],
            ['ABio', 'Autorização de Captura, Coleta e Transporte de Material Biológico'],
            ['OUT', 'Outorga de Direito de Uso de Recursos Hídricos'],
            ['DL', 'Dispensa de Licenciamento'],
        ]));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sgc_licencas_alertas');
        Schema::dropIfExists('sgc_licencas_prazo_registros');
        Schema::dropIfExists('sgc_licencas_prazos');
        Schema::dropIfExists('sgc_licencas_condicionantes');
        Schema::dropIfExists('sgc_licencas_responsaveis');
        Schema::dropIfExists('sgc_licencas');
        Schema::dropIfExists('sgc_licencas_tipos');
    }
};
