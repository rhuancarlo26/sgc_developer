<?php

namespace Tests\Unit;

use App\Domain\Sgc\Contratada\RelatorioCoord\Services\AnexosRelatorioService;
use App\Domain\Sgc\Contratada\RelatorioCoord\Services\UpdateStatusService;
use App\Domain\Sgc\Contratada\RelatorioCoord\Services\UploadService;
use App\Domain\Sgc\Contratada\RelatorioCoord\Controller\RelatorioCoordenacaoController;
use App\Domain\Sgc\Contratada\RelatorioCoord\Controller\StoreUploadRelatorioController;
use App\Models\SgcHistoricoRelatorio;
use App\Models\SgcRelatorioCoordenacao;
use App\Models\SgcRelatorioUpload;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RelatorioRevisoesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        foreach (['sgc_relatorio_coordenacao', 'sgc_historico_relatorios'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                foreach (['contrato_id', 'relatorio_num', 'id_item', 'versao', 'aprovado'] as $field) $table->integer($field)->nullable();
                foreach (['nome_topico', 'status', 'periodo', 'caminho', 'data_atualizacao'] as $field) $table->string($field)->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
        Schema::create('sgc_upload', function (Blueprint $table) {
            $table->id();
            foreach (['contrato_id', 'num_relatorio', 'item_id', 'versao'] as $field) $table->integer($field);
            foreach (['nome', 'tipo', 'caminho'] as $field) $table->string($field);
            $table->timestamps();
        });
        SgcRelatorioCoordenacao::create(['contrato_id' => 6, 'relatorio_num' => 18, 'id_item' => 1, 'nome_topico' => 'Topico', 'status' => 'Analise DNIT']);
    }

    private function anexo(int $versao, string $path, int $contrato = 6): SgcRelatorioUpload
    {
        return SgcRelatorioUpload::create(['contrato_id' => $contrato, 'num_relatorio' => 18, 'item_id' => 1, 'versao' => $versao, 'nome' => $path, 'tipo' => 'docx', 'caminho' => $path]);
    }

    private function enviar(string $nome, int $item = 1): SgcRelatorioUpload
    {
        $service = new class(app(\App\Shared\Utils\DataManagement::class)) extends UploadService {
            public function storageAnexo($arquivo) { return ['nome' => $arquivo->getClientOriginalName(), 'tipo' => 'docx', 'caminho' => $arquivo->getClientOriginalName()]; }
        };
        return $service->salvarAnexo(['contrato_id' => 6, 'relatorio_num' => 18, 'item_id' => $item, 'arquivo' => UploadedFile::fake()->create($nome)])['request'];
    }

    public function test_revisions_keep_previous_files_and_copy_only_one_file_per_topic(): void
    {
        $this->anexo(0, 'original.docx');
        $service = new UpdateStatusService;
        for ($versao = 1; $versao <= 5; $versao++) {
            $service->revisaoStatus(6, 18);
            $copiado = AnexosRelatorioService::consulta(6, 18)->firstOrFail();
            $this->assertSame($versao, (int) $copiado->versao);
            $this->assertSame($versao === 1 ? 'original.docx' : 'rev'.($versao - 1).'.docx', $copiado->caminho);
            $this->enviar('rev'.$versao.'.docx');
            $this->assertSame('rev'.$versao.'.docx', AnexosRelatorioService::consulta(6, 18)->firstOrFail()->caminho);
            $service->updateStatus(6, 18);
        }
        $this->assertSame(6, SgcRelatorioUpload::count());
        $this->assertSame(5, SgcHistoricoRelatorio::count());
        $this->assertSame('original.docx', AnexosRelatorioService::consulta(6, 18, 0)->firstOrFail()->caminho);
        $this->assertSame('rev1.docx', AnexosRelatorioService::consulta(6, 18, 1)->firstOrFail()->caminho);
    }

    public function test_legacy_duplicates_prefer_updated_file_for_upload_read_and_next_copy(): void
    {
        $this->anexo(0, 'original.docx');
        (new UpdateStatusService)->revisaoStatus(6, 18);
        (new UpdateStatusService)->revisaoStatus(6, 18);
        $edited = AnexosRelatorioService::consulta(6, 18, 2)->firstOrFail();
        $edited->update(['caminho' => 'edited.docx', 'updated_at' => '2020-01-01 12:00:00']);
        $stale = $this->anexo(2, 'stale.docx');
        $stale->update(['updated_at' => '2019-01-01 12:00:00']);
        $this->anexo(2, 'other-contract.docx', 7);
        $this->assertSame($edited->id, AnexosRelatorioService::consulta(6, 18)->firstOrFail()->id);
        $result = $this->enviar('latest.docx');
        $this->assertSame($edited->id, $result->id);
        $this->assertSame('latest.docx', AnexosRelatorioService::consulta(6, 18)->firstOrFail()->caminho);
        (new UpdateStatusService)->revisaoStatus(6, 18);
        $this->assertSame(1, SgcRelatorioUpload::where('contrato_id', 6)->where('versao', 3)->count());
        $this->assertSame('latest.docx', AnexosRelatorioService::consulta(6, 18)->firstOrFail()->caminho);
        $this->assertSame(2, SgcRelatorioUpload::where('contrato_id', 6)->where('versao', 2)->count());
    }

    public function test_first_upload_of_a_missing_topic_uses_report_revision(): void
    {
        (new UpdateStatusService)->revisaoStatus(6, 18);
        (new UpdateStatusService)->revisaoStatus(6, 18);
        $result = $this->enviar('late.docx', 2);
        $this->assertSame(2, (int) $result->versao);
        $this->assertNull(AnexosRelatorioService::consulta(6, 18, 0)->where('item_id', 2)->first());
        $this->assertSame('late.docx', AnexosRelatorioService::consulta(6, 18)->where('item_id', 2)->firstOrFail()->caminho);
    }

    public function test_docx_and_download_select_same_updated_record_and_respect_history(): void
    {
        config(['filesystems.default' => 'local']);
        Storage::fake('local');
        $this->anexo(0, 'original.docx');
        (new UpdateStatusService)->revisaoStatus(6, 18);
        (new UpdateStatusService)->revisaoStatus(6, 18);
        $this->anexo(2, 'latest.docx');
        Storage::put('public/latest.docx', 'latest');
        Storage::put('public/original.docx', 'original');
        $controller = app(RelatorioCoordenacaoController::class);
        $current = $controller->getDocx(1, 6, 2, 18);
        $history = $controller->getDocx(1, 6, 0, 18);
        $download = app(StoreUploadRelatorioController::class)->downloadAnexo(6, 1, 18, \Illuminate\Http\Request::create('/download'));
        $this->assertStringContainsString('latest.docx', $current->headers->get('Content-Disposition'));
        $this->assertStringContainsString('original.docx', $history->headers->get('Content-Disposition'));
        $this->assertSame($current->headers->get('Content-Disposition'), $download->headers->get('Content-Disposition'));
    }
    public function test_history_is_scoped_to_contract_and_revision(): void
    {
        Schema::create('contratos', function (Blueprint $table) { $table->id(); });
        DB::table('contratos')->insert(['id' => 6]);
        foreach ([[6, 1], [7, 1], [6, 0]] as [$contrato, $versao]) {
            SgcHistoricoRelatorio::create(['contrato_id' => $contrato, 'relatorio_num' => 18, 'id_item' => 1, 'versao' => $versao]);
        }
        $request = \Illuminate\Http\Request::create('/history');
        $request->headers->set('X-Inertia', 'true');
        $request->headers->set('X-Inertia-Partial-Component', 'Sgc/Contratada/Relatorio/Historico');
        $request->headers->set('X-Inertia-Partial-Data', 'historico');
        $response = app(RelatorioCoordenacaoController::class)->showHistorico(6, 18, 1)->toResponse($request);
        $historico = $response->getData(true)['props']['historico'];
        $this->assertCount(1, $historico);
        $this->assertSame(6, $historico[0]['contrato_id']);
        $this->assertSame(1, $historico[0]['versao']);
    }

    public function test_missing_requested_revision_never_opens_an_older_file(): void
    {
        $this->anexo(0, 'original.docx');
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        app(RelatorioCoordenacaoController::class)->getDocx(1, 6, 2, 18);
    }

    public function test_annex_download_respects_selected_historical_revision_including_zero(): void
    {
        config(['filesystems.default' => 'local']);
        Storage::fake('local');
        (new UpdateStatusService)->revisaoStatus(6, 18);
        (new UpdateStatusService)->revisaoStatus(6, 18);
        foreach ([18, 19, 20] as $item) {
            foreach ([0, 1, 2] as $versao) {
                $path = 'anexo'.$item.'-rev'.$versao.'.pdf';
                $this->anexo($versao, $path)->update(['item_id' => $item]);
                Storage::put('public/'.$path, 'conteudo '.$versao);
            }
            foreach ([0, 1] as $versao) {
                $request = \Illuminate\Http\Request::create('/download', 'GET', ['versao' => (string) $versao]);
                $response = app(StoreUploadRelatorioController::class)->downloadAnexo(6, $item, 18, $request);
                $this->assertStringContainsString('anexo'.$item.'-rev'.$versao.'.pdf', $response->headers->get('Content-Disposition'));
            }
        }
    }

}
