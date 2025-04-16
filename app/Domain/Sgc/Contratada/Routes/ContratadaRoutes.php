<?php

use App\Domain\Contrato\Contratada\DadosGerais\Empreendimento\Controller\StoreContratadaEmpreendimentoTrechoController;
use Illuminate\Support\Facades\Route;

use App\Domain\Sgc\Contratada\app\Controller\ContratoSgcController;
use App\Domain\Sgc\Contratada\app\Controller\DashplanController;
use App\Domain\Sgc\Contratada\app\Controller\EmpreendimentosController;
use App\Domain\Sgc\Contratada\RelatorioCoord\Controller\RelatorioCoordenacaoController;
use App\Domain\Sgc\Contratada\Comentario\Controller\DestroyComentariosController;
use App\Domain\Sgc\Contratada\Comentario\Controller\StoreSgcComentarioController;
use App\Domain\Sgc\Contratada\Comentario\Controller\StoreSgcComentariosController;
use App\Domain\Sgc\Contratada\Coordenadas\CoordenadasController;
use App\Domain\Sgc\Contratada\RelatorioCoord\Controller\StoreUploadRelatorioController;
use App\Domain\Sgc\Contratada\RelatorioCoord\Controller\VisualizarDocxController;;
use App\Domain\Sgc\Contratada\RelatorioCoord\Controller\StatusUpdateController;
use App\Domain\Sgc\Contratada\RelatorioCoord\Controller\CreateController;
use App\Domain\Sgc\Contratada\Dav\Controller\ListagemDavController;
use App\Domain\Sgc\Contratada\Cronograma\Controller\CronogController;
use App\Mail\StatusChanged;
use Illuminate\Support\Facades\Mail;



Route::prefix('/contratada')->group(function () {
    Route::get('{contrato}/',                                             [ContratoSgcController::class,                           'index'])->name('sgc.contratada.index');
    Route::get('{contrato}/relatorio',                                    [RelatorioCoordenacaoController::class,                  'index'])->name('sgc.contratada.relatorio.index');
    Route::get('{contrato}/relatorios',                                   [RelatorioCoordenacaoController::class,                  'relatorios'])->name('sgc.contratada.relatorios.index');
    Route::get('{contrato}/relatorio/{relatorio_num}',                    [RelatorioCoordenacaoController::class,                  'index'])->name('sgc.contratada.relatorio.detalhes');
    Route::get('{contrato}/relatorio/{relatorio_num}/historico/{versao}', [RelatorioCoordenacaoController::class,                  'showHistorico'])->name('sgc.contratada.relatorio.historico');

    Route::get('/send-email/{contrato}/{status}/{relatorio_num}', function ($contrato, $status, $relatorio_num) {
        $toEmail = 'rhuan.borges@jgpconsultoria.com.br';

        //Mail::to($toEmail)->send(new StatusChanged($status, $contrato, $relatorio_num));

        return 'E-mail enviado!';
    })->name('sgc.contratada.send-email');

    // Download anexo
    Route::get('/sgc/contratada/download-anexo/{contratoId}/{itemId}/{relatorioNum}', [StoreUploadRelatorioController::class, 'downloadAnexo'])->name('sgc.contratada.download_anexo');

    // Relatório de Coordenação
    Route::get('/sgc/visualizar',                                  [VisualizarDocxController::class,                        'index'])->name('sgc.contratada.visualizar_doc');

    Route::get('/sgc/relatorio-coordenacao', function () {
        $itens = App\Models\SgcRelatorioCoordenacao::all();
        return response()->json($itens);
    })->name('sgc.relatorio_coordenacao.index');

    Route::get('/sgc/relatorio-coordenacao-item', function () {
        $dados = App\Models\SgcRelatorioUpload::select('item_id', 'caminho', 'contrato_id', 'num_relatorio', 'versao', 'num_relatorio', 'versao')->get();
        return response()->json($dados);
    })->name('sgc.relatorio_coordenacao_upload.index');

    // Atualizar Status Relatório
    Route::post('/store_upload',                                   [StoreUploadRelatorioController::class,                  'index'])->name('sgc.contratada.store_anexo');
    Route::post('/sgc/relatorio-coordenacao/update-status',        [StatusUpdateController::class,                          'updateStatus'])->name('sgc.relatorio_coordenacao.update_status');
    Route::post('/sgc/relatorio-coordenacao/revisao-status',       [StatusUpdateController::class,                          'revisaoStatus'])->name('sgc.relatorio_coordenacao.revisao_status');
    Route::post('/sgc/relatorio-coordenacao/aprovado-status',      [StatusUpdateController::class,                          'aprovadoStatus'])->name('sgc.relatorio_coordenacao.aprovado_status');

    Route::post('{contrato}/dashboard-searchempreendimentos',      [DashplanController::class,                              'searchempreendimentos'])->name('sgc.contratada.dashboard.searchempreendimentos');

    Route::post('/relatorio-coordenacao/toggle-aprovado/{id}',     [RelatorioCoordenacaoController::class,                  'toggleAprovado'])->name('sgc.relatorio_coordenacao.toggle_aprovado');
    Route::post('/relatorio-coordenacao/toggle-aprovado/{id}',     [RelatorioCoordenacaoController::class,                  'toggleAprovado'])->name('sgc.relatorio_coordenacao.toggle_aprovado');

    Route::post('/sgc/store_comentario',                           [StoreSgcComentarioController::class,                    'index'])->name('sgc.contratada.store_comentario');
    Route::post('/sgc/store_comentarios',                          [StoreSgcComentariosController::class,                   'index'])->name('sgc.contratada.store_comentarios');
    Route::delete('/destroy_comentarios/{comentarios}',            [DestroyComentariosController::class,                    'index'])->name('sgc.contratada.destroy_comentarios');
    Route::delete('/destroy_comentarios/{comentarios}',            [DestroyComentariosController::class,                    'index'])->name('sgc.contratada.destroy_comentarios');

    // Inserir novo Relatório de Coordenação
    Route::post('/sgc/relatorio/iniciar',                          [CreateController::class,                                 'index'])->name('sgc.contratada.relatorio.iniciar');

    //DAV's
    Route::put('/sgc/gestao/dav/{id}/aprovar', [ListagemDavController::class, 'aprovar'])->name('sgc.gestao.aprovarDav');
    Route::put('/sgc/gestao/dav/{id}/reprovar', [ListagemDavController::class, 'reprovar'])->name('sgc.gestao.reprovarDav');

    //Calendário Cronograma
     Route::get('{contrato}/cronograma', [CronogController::class, 'index'])->name('sgc.contratada.cronograma.index');
     Route::get('{contrato}/cronograma/opcoes-evento', [CronogController::class, 'getOpcoesEvento'])->name('sgc.contratada.cronograma.opcoesEvento');
     Route::post('{contrato}/cronograma/evento-auxiliar', [CronogController::class, 'storeEventoAuxiliar'])->name('sgc.contratada.cronograma.storeEventoAuxiliar');

    // Update Fóruns
    Route::post('/sgc/empreendimento/{id}/updatecampo', [EmpreendimentosController::class, 'updatecampo'])->name('sgc.contratada.empreendimento.updatecampo');

});
