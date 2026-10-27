<?php

namespace Tests\Unit;

use App\Domain\Sgc\Contratada\RelatorioCoord\Requests\UpdatePeriodoRelatorioRequest;
use App\Domain\Sgc\Contratada\RelatorioCoord\Services\UpdatePeriodoRelatorioService;
use App\Models\ChangeLog;
use App\Models\SgcHistoricoRelatorio;
use App\Models\SgcRelatorioCoordenacao;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class RelatorioEdicaoPeriodoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('users', function (Blueprint $table) { $table->id(); $table->string('name'); });
        DB::table('users')->insert(['id' => 5, 'name' => 'Master']);
        (require database_path('migrations/2025_04_15_160353_create_change_logs_table.php'))->up();
        foreach (['sgc_relatorio_coordenacao', 'sgc_historico_relatorios'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                foreach (['contrato_id', 'relatorio_num', 'id_item', 'versao', 'aprovado'] as $field) $table->integer($field)->nullable();
                foreach (['nome_topico', 'status', 'periodo', 'caminho', 'data_atualizacao'] as $field) $table->string($field)->nullable();
                $table->timestamps(); $table->softDeletes();
            });
        }
    }

    private function request(bool $master): UpdatePeriodoRelatorioRequest
    {
        $request = UpdatePeriodoRelatorioRequest::create('/periodo', 'PATCH', ['data_inicio' => '2026-08-01', 'data_fim' => '2026-09-30']);
        $user = \Mockery::mock(\App\Models\User::class)->makePartial();
        $user->shouldReceive('hasRole')->with('Super Admin')->andReturn($master);
        $request->setUserResolver(fn () => $user);
        $request->setContainer(app())->setRedirector(app('redirect'));
        return $request;
    }

    public function test_non_master_is_denied_on_backend(): void
    {
        $this->expectException(AuthorizationException::class);
        $this->request(false)->validateResolved();
    }

    public function test_master_can_validate_the_period_without_contract_in_payload(): void
    {
        $request = $this->request(true);
        $request->validateResolved();
        $this->assertSame(['data_inicio' => '2026-08-01', 'data_fim' => '2026-09-30'], $request->validated());
        $this->assertFalse((new UpdatePeriodoRelatorioRequest)->authorize());
    }

    public function test_invalid_dates_are_rejected(): void
    {
        $rules = (new UpdatePeriodoRelatorioRequest)->rules();
        foreach ([[], ['data_inicio' => '2026-02-30', 'data_fim' => '2026-03-31'], ['data_inicio' => '2026-09-30', 'data_fim' => '2026-08-01']] as $data) {
            $this->assertTrue(Validator::make($data, $rules)->fails());
        }
    }

    public function test_correction_updates_topics_and_revisions_only_for_requested_report_with_audit(): void
    {
        foreach ([[6, 20], [6, 20], [7, 20], [6, 21]] as [$contrato, $numero]) {
            foreach ([SgcRelatorioCoordenacao::class, SgcHistoricoRelatorio::class] as $model) {
                $model::create(['contrato_id' => $contrato, 'relatorio_num' => $numero, 'id_item' => 1, 'versao' => 0, 'periodo' => '01/01/2000 a 01/01/2000', 'status' => 'Relatório Aprovado', 'aprovado' => 1]);
            }
        }
        $before = SgcRelatorioCoordenacao::first()->updated_at->toDateTimeString();
        $this->travelTo(now()->addDay());
        $service = new UpdatePeriodoRelatorioService;
        $service->atualizar(6, 20, '2026-08-01', '2026-09-30', 5);
        foreach ([SgcRelatorioCoordenacao::class, SgcHistoricoRelatorio::class] as $model) {
            $this->assertSame(2, $model::where('periodo', '01/08/2026 a 30/09/2026')->count());
            $this->assertSame(2, $model::where('periodo', '01/01/2000 a 01/01/2000')->count());
            $this->assertSame(4, $model::where('status', 'Relatório Aprovado')->where('aprovado', 1)->count());
        }
        $this->assertSame($before, SgcRelatorioCoordenacao::first()->updated_at->toDateTimeString());
        $this->assertSame(4, ChangeLog::count());
        foreach (ChangeLog::all() as $log) {
            $this->assertSame(5, $log->user_id);
            $this->assertSame('periodo', $log->field);
            $this->assertSame('01/01/2000 a 01/01/2000', $log->old_value);
            $this->assertSame('01/08/2026 a 30/09/2026', $log->new_value);
        }
        $service->atualizar(6, 20, '2026-08-01', '2026-09-30', 5);
        $this->assertSame(4, ChangeLog::count());
        $this->travelBack();
    }

    public function test_missing_report_does_not_create_or_modify_records(): void
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        (new UpdatePeriodoRelatorioService)->atualizar(6, 999, '2026-08-01', '2026-09-30', 5);
    }
}
