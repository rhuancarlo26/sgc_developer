<?php

namespace Tests\Unit;

use App\Domain\Sgc\Contratada\RelatorioCoord\Services\UpdateStatusService;
use App\Models\ChangeLog;
use App\Models\SgcRelatorioCoordenacao;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RelatorioAprovacaoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('users', function (Blueprint $table) { $table->id(); $table->string('name'); });
        $migration = require database_path('migrations/2025_04_15_160353_create_change_logs_table.php');
        $migration->up();
        Schema::create('sgc_relatorio_coordenacao', function (Blueprint $table) {
            $table->id(); $table->integer('contrato_id'); $table->integer('relatorio_num');
            $table->integer('id_item'); $table->string('status');
            foreach (['nome_topico', 'periodo', 'caminho', 'data_atualizacao'] as $campo) $table->string($campo)->nullable();
            $table->integer('aprovado')->nullable(); $table->integer('versao')->nullable();
            $table->timestamps(); $table->softDeletes();
        });
        DB::table('users')->insert([['id' => 5, 'name' => 'Fiscal responsavel'], ['id' => 8, 'name' => 'Outro usuario']]);
        $this->actingAs((new User)->forceFill(['id' => 5, 'name' => 'Fiscal responsavel'])->setRelation('roles', collect()));
    }

    public function test_approval_records_authenticated_user_and_date_for_every_topic_in_the_correct_report(): void
    {
        foreach ([[6, 26, 1], [6, 26, 2], [7, 26, 1], [6, 25, 1]] as [$contrato, $numero, $item]) {
            SgcRelatorioCoordenacao::create(['contrato_id' => $contrato, 'relatorio_num' => $numero, 'id_item' => $item, 'status' => 'Analise DNIT']);
        }
        $this->travelTo(\Carbon\Carbon::parse('2026-10-14 10:45:54', 'America/Sao_Paulo'));
        (new UpdateStatusService)->aprovadoStatus(6, 26);
        $this->assertSame(2, ChangeLog::count());
        foreach (SgcRelatorioCoordenacao::with('aprovacao.user:id,name')->where('contrato_id', 6)->where('relatorio_num', 26)->get() as $item) {
            $this->assertSame('Relatório Aprovado', $item->status);
            $this->assertSame(5, $item->aprovacao->user_id);
            $this->assertSame('Fiscal responsavel', $item->aprovacao->user->name);
            $this->assertSame('2026-10-14 10:45:54', $item->aprovacao->created_at->format('Y-m-d H:i:s'));
        }
        $this->assertSame(2, SgcRelatorioCoordenacao::where('status', 'Analise DNIT')->count());
        $this->travelBack();
    }

    public function test_repeated_approval_preserves_original_author_and_timestamp(): void
    {
        $item = SgcRelatorioCoordenacao::create(['contrato_id' => 6, 'relatorio_num' => 26, 'id_item' => 1, 'status' => 'Analise DNIT']);
        $service = new UpdateStatusService;
        $service->aprovadoStatus(6, 26);
        $primeira = $item->fresh()->aprovacao;
        $this->actingAs((new User)->forceFill(['id' => 8]));
        $service->aprovadoStatus(6, 26);
        $this->assertSame(1, ChangeLog::count());
        $this->assertSame(5, $item->fresh()->aprovacao->user_id);
        $this->assertTrue($primeira->created_at->equalTo($item->fresh()->aprovacao->created_at));
    }

    public function test_old_approval_does_not_invent_author_or_use_unrelated_audit_record(): void
    {
        $item = SgcRelatorioCoordenacao::create(['contrato_id' => 6, 'relatorio_num' => 18, 'id_item' => 1, 'status' => 'Relatório Aprovado']);
        ChangeLog::create(['user_id' => 5, 'table_name' => 'outra_tabela', 'record_id' => $item->id, 'field' => 'status', 'new_value' => 'Relatório Aprovado']);
        (new UpdateStatusService)->aprovadoStatus(6, 18);
        $this->assertNull($item->fresh()->aprovacao);
        $this->assertSame(1, ChangeLog::count());
    }
    public function test_report_list_response_includes_approval_author_and_timestamp(): void
    {
        Schema::create('sgc_historico_relatorios', function (Blueprint $table) {
            $table->id(); $table->integer('contrato_id');
            $table->integer('relatorio_num'); $table->integer('versao');
        });
        SgcRelatorioCoordenacao::create(['contrato_id' => 6, 'relatorio_num' => 25, 'id_item' => 0, 'status' => 'Analise DNIT']);
        (new UpdateStatusService)->aprovadoStatus(6, 25);
        $contrato = (new \App\Models\Contrato)->forceFill(['id' => 6]);
        $request = \Illuminate\Http\Request::create('/relatorios');
        $request->headers->set('X-Inertia', 'true');
        $request->headers->set('X-Inertia-Partial-Component', 'Sgc/Contratada/Relatorio/Relatorios');
        $request->headers->set('X-Inertia-Partial-Data', 'dadosrelat');
        $controller = app(\App\Domain\Sgc\Contratada\RelatorioCoord\Controller\RelatorioCoordenacaoController::class);
        $response = $controller->relatorios($contrato)->toResponse($request);
        $registro = $response->getData(true)['props']['dadosrelat'][0];
        $this->assertSame('Fiscal responsavel', $registro['aprovacao']['user']['name']);
        $this->assertSame(5, $registro['aprovacao']['user_id']);
        $this->assertNotEmpty($registro['aprovacao']['created_at']);
        $this->assertSame(25, $registro['relatorio_num']);
    }

}
