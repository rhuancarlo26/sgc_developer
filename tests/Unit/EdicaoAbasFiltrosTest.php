<?php
namespace Tests\Unit;

use App\Domain\Sgc\Contratada\app\Controller\EmpreendimentosController;
use App\Exports\EmpreendimentoExport;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EdicaoAbasFiltrosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        foreach (['sgcvw_estudos', 'sgcvw_subprodutos'] as $tabela) {
            Schema::create($tabela, function (Blueprint $t) { $t->id(); $t->string('contrato'); $t->string('subproduto'); });
            for ($i = 1; $i <= 60; $i++) DB::table($tabela)->insert(['contrato' => $i <= 55 ? '94/2022' : '99/2022', 'subproduto' => '3.1.1']);
        }
        Schema::create('change_logs', function (Blueprint $t) { $t->id(); $t->integer('record_id'); $t->string('table_name'); $t->integer('user_id')->nullable(); $t->timestamps(); });
    }

    public function test_contract_filter_is_applied_before_pagination_and_survives_links(): void
    {
        foreach (['editavelestudos', 'editavelprodutos'] as $metodo) {
            $request = Request::create('/edicao', 'GET', ['contrato' => '94/2022', 'ordenarPor' => 'id', 'ordem' => 'desc']);
            $response = app(EmpreendimentosController::class)->{$metodo}($request);
            $property = new \ReflectionProperty($response, 'props');
            $property->setAccessible(true);
            $props = $property->getValue($response);
            $lista = $props['empreendimentos'];
            $this->assertSame(55, $lista->total());
            $this->assertSame(50, $lista->count());
            $this->assertSame(55, $lista->items()[0]->id);
            $this->assertStringContainsString('contrato=94%2F2022', $lista->nextPageUrl());
            $this->assertSame(['94/2022', '99/2022'], $props['contratosDisponiveis']->all());
            $this->assertSame('94/2022', $props['filtros']['contrato']);
        }
    }

    public function test_excel_export_includes_all_filtered_pages_and_keeps_default_export(): void
    {
        foreach (['sgcvw_estudos', 'sgcvw_subprodutos'] as $tabela) {
            $filtrado = (new EmpreendimentoExport(['id', 'contrato'], $tabela, 'id', 'desc', '94/2022'))->collection();
            $this->assertCount(55, $filtrado);
            $this->assertSame(55, $filtrado->first()->id);
            $this->assertSame(['94/2022'], $filtrado->pluck('contrato')->unique()->values()->all());
            $this->assertCount(60, (new EmpreendimentoExport(['id'], $tabela, 'id'))->collection());
        }
    }
}
