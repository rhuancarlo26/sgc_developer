<?php
namespace Tests\Unit;

use App\Domain\Sgc\Contratada\Produtos\Fauna\Requests\StoreEntregaSimplificadaFaunaRequest;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Services\SgcFaunaEntregaSimplificadaService;
use App\Models\SgcFaunaCampanha;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class FaunaDatasSimplificadaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('sgc_fauna_campanha', function (Blueprint $t) {
            $t->id();
            foreach (['id_contrato', 'id_campanha', 'versao_analise'] as $campo) $t->integer($campo);
            foreach (['cod_emp', 'subproduto', 'modo_preenchimento', 'status', 'etapa_atual'] as $campo) $t->string($campo);
            $t->string('sei_dnit')->nullable();
            $t->date('data_ini')->nullable(); $t->date('data_fim')->nullable(); $t->timestamps();
        });
        Schema::create('sgc_entregas_simplificadas', function (Blueprint $t) {
            $t->id();
            foreach (['id_contrato', 'entidade_id', 'versao_analise'] as $campo) $t->integer($campo);
            foreach (['produto_tipo', 'entidade_tipo', 'status'] as $campo) $t->string($campo);
            $t->timestamps();
        });
        foreach (['sgc_fauna_entrega_simplificada_planilhas', 'sgc_entrega_simplificada_arquivos', 'sgc_entrega_simplificada_analises'] as $tabela) {
            Schema::create($tabela, function (Blueprint $t) { $t->id(); $t->integer('entrega_simplificada_id'); $t->string('tipo')->nullable(); });
        }
    }

    public function test_create_edit_preserve_and_clear_existing_date_columns(): void
    {
        $service = new SgcFaunaEntregaSimplificadaService();
        $dados = ['contrato_id' => 6, 'cod_emp' => '230/MA', 'id_campanha' => 1, 'subproduto' => 'Fauna', 'enviar_analise' => false, 'data_ini' => '2026-04-01', 'data_fim' => '2026-04-15'];
        $entrega = $service->criar($dados);
        $campanha = SgcFaunaCampanha::findOrFail($entrega->entidade_id);
        $this->assertSame('2026-04-01', $campanha->data_ini);
        $this->assertSame('2026-04-15', $campanha->data_fim);
        $service->atualizar($campanha, $entrega, array_replace($dados, ['data_ini' => '2026-05-01', 'data_fim' => '2026-05-10']));
        $this->assertSame('2026-05-01', $campanha->fresh()->data_ini);
        $this->assertSame('2026-05-10', $campanha->fresh()->data_fim);
        unset($dados['data_ini'], $dados['data_fim']);
        $service->atualizar($campanha, $entrega, $dados);
        $this->assertSame('2026-05-01', $campanha->fresh()->data_ini);
        $service->atualizar($campanha, $entrega, array_replace($dados, ['data_ini' => null, 'data_fim' => null]));
        $this->assertNull($campanha->fresh()->data_ini);
        $this->assertNull($campanha->fresh()->data_fim);
    }

    public function test_period_validation_accepts_empty_drafts_and_rejects_invalid_dates(): void
    {
        $regras = (new StoreEntregaSimplificadaFaunaRequest())->rules();
        $regras = array_intersect_key($regras, array_flip(['data_ini', 'data_fim']));
        foreach ([[], ['data_ini' => null, 'data_fim' => null], ['data_ini' => '2026-04-01', 'data_fim' => '2026-04-01']] as $dados) $this->assertTrue(Validator::make($dados, $regras)->passes());
        foreach ([['data_ini' => '2026-04-01'], ['data_fim' => '2026-04-01'], ['data_ini' => '2026-05-01', 'data_fim' => '2026-04-30'], ['data_ini' => '2026-02-30', 'data_fim' => '2026-03-01']] as $dados) $this->assertTrue(Validator::make($dados, $regras)->fails());
    }
}
