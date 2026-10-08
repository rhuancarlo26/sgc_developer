<?php

namespace Tests\Unit;

use App\Domain\Sgc\Contratada\RelatorioCoord\Requests\CreateRelatorioRequest;
use App\Domain\Sgc\Contratada\RelatorioCoord\Services\CreateRelatorioService;
use App\Models\SgcRelatorioCoordenacao;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class RelatorioPeriodoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('sgc_relatorio_coordenacao', function (Blueprint $table) {
            $table->id();
            $table->integer('contrato_id');
            $table->integer('relatorio_num');
            $table->integer('id_item');
            $table->string('nome_topico');
            $table->string('status');
            $table->integer('aprovado');
            $table->string('periodo');
            $table->integer('versao');
            $table->timestamps();
        });
    }

    public function test_period_is_saved_for_all_topics_and_numbering_stays_per_contract(): void
    {
        $service = new CreateRelatorioService();
        $service->iniciarNovoRelatorio(['id' => 6], '2026-04-01', '2026-05-31');
        $service->iniciarNovoRelatorio(['id' => 6], '2026-06-01', '2026-07-31');
        $service->iniciarNovoRelatorio(['id' => 7], '2026-04-01', '2026-05-31');
        $query = SgcRelatorioCoordenacao::where('contrato_id', 6);
        $this->assertSame(20, (clone $query)->where('relatorio_num', 1)->count());
        $this->assertSame(['01/04/2026 a 31/05/2026'], (clone $query)->where('relatorio_num', 1)->distinct()->pluck('periodo')->all());
        $this->assertSame(['01/06/2026 a 31/07/2026'], (clone $query)->where('relatorio_num', 2)->distinct()->pluck('periodo')->all());
        $this->assertSame(1, SgcRelatorioCoordenacao::where('contrato_id', 7)->max('relatorio_num'));
    }

    public function test_invalid_or_missing_periods_are_rejected(): void
    {
        $rules = (new CreateRelatorioRequest())->rules();
        foreach ([
            ['contrato' => ['id' => 6]],
            ['contrato' => ['id' => 6], 'data_inicio' => '2026-05-01', 'data_fim' => '2026-04-30'],
            ['contrato' => ['id' => 6], 'data_inicio' => '2026-02-30', 'data_fim' => '2026-03-31'],
        ] as $dados) {
            $this->assertTrue(Validator::make($dados, $rules)->fails());
        }
        $this->assertTrue(Validator::make(['contrato' => ['id' => 6], 'data_inicio' => '2026-04-01', 'data_fim' => '2026-05-31'], $rules)->passes());
    }
}
