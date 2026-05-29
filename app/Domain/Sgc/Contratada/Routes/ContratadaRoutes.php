<?php

use App\Domain\Contrato\Contratada\DadosGerais\Empreendimento\Controller\StoreContratadaEmpreendimentoTrechoController;
use Illuminate\Support\Facades\Route;
use App\Domain\Sgc\Contratada\app\Controller\ContratoSgcController;
use App\Domain\Sgc\Contratada\app\Controller\DashplanController;
use App\Domain\Sgc\Contratada\app\Controller\EmpreendimentosController;
use App\Domain\Sgc\Contratada\Comentario\Controller\DestroyComentarioController;
use App\Domain\Sgc\Contratada\RelatorioCoord\Controller\RelatorioCoordenacaoController;
use App\Domain\Sgc\Contratada\Comentario\Controller\DestroyComentariosController;
use App\Domain\Sgc\Contratada\Comentario\Controller\StoreSgcComentarioController;
use App\Domain\Sgc\Contratada\Comentario\Controller\StoreSgcComentariosController;
use App\Domain\Sgc\Contratada\RelatorioCoord\Controller\StoreUploadRelatorioController;
use App\Domain\Sgc\Contratada\RelatorioCoord\Controller\VisualizarDocxController;;
use App\Domain\Sgc\Contratada\RelatorioCoord\Controller\StatusUpdateController;
use App\Domain\Sgc\Contratada\RelatorioCoord\Controller\CreateController;
use App\Domain\Sgc\Contratada\Dav\Controller\ListagemDavController;
use App\Domain\Sgc\Contratada\Cronograma\Controller\CronogController;
use App\Domain\Sgc\Contratada\Ficha\Controller\FichaController;
use App\Domain\Sgc\Contratada\Quantitativos\Controller\QuantitativosController;
use App\Domain\Sgc\Contratada\Produtos\Controller\ProdutosController;
use App\Domain\Sgc\Contratada\Produtos\Controller\StoreProdutoAbioController;
use App\Domain\Sgc\Contratada\Produtos\Espeleologia\Controller\EspeleoCampanhaController;
use App\Domain\Sgc\Contratada\Produtos\Espeleologia\Controller\MapLayerController;
use App\Domain\Sgc\Contratada\Produtos\Espeleologia\Controller\MapPageController;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Controller\AnexoController;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Controller\CampanhaController;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Controller\ComentarioController;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Controller\ProfissionalController;
use App\Domain\Sgc\Contratada\Produtos\Pmqa\Controller\PmqaCampanhaController;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Controller\DestroyCampanhaController;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Controller\InitializarRascunhoCampanhaController;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Controller\SalvarEtapaCampanhaController;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Controller\SubmeterCampanhaController;

use App\Mail\StatusChanged;
use Illuminate\Support\Facades\Mail;

Route::prefix('/contratada')->middleware(['route-permission'])->group(function () {
    Route::get('{contrato}/',                                             [ContratoSgcController::class,                           'index'])->name('sgc.contratada.index');
    Route::get('{contrato}/relatorio',                                    [RelatorioCoordenacaoController::class,                  'index'])->name('sgc.contratada.relatorio.index');
    Route::get('{contrato}/relatorios',                                   [RelatorioCoordenacaoController::class,                  'relatorios'])->name('sgc.contratada.relatorios.index');
    Route::get('{contrato}/relatorio/{relatorio_num}',                    [RelatorioCoordenacaoController::class,                  'index'])->name('sgc.contratada.relatorio.detalhes');
    Route::get('{contrato}/relatorio/{relatorio_num}/historico/{versao}', [RelatorioCoordenacaoController::class,                  'showHistorico'])->name('sgc.contratada.relatorio.historico');

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
    Route::post('/sgc/store_comentario',                           [StoreSgcComentarioController::class,                    'index'])->name('sgc.contratada.store_comentario');
    Route::post('/sgc/store_comentarios',                          [StoreSgcComentariosController::class,                   'index'])->name('sgc.contratada.store_comentarios');
    Route::delete('/destroy_comentarios/{comentarios}',            [DestroyComentariosController::class,                    'index'])->name('sgc.contratada.destroy_comentarios');
    Route::delete('/destroy_comentario/{comentario}',              [DestroyComentarioController::class,                     'destroy'])->name('sgc.contratada.destroy_comentario');

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

    // Ficha Contratual
    Route::get('{contrato}/ficha', [FichaController::class, 'index'])->name('sgc.contratada.ficha.index');

    // Quantitativos
    Route::get('{contrato}/quantitativos', [QuantitativosController::class, 'index'])->name('sgc.contratada.quantitativos.index');

    // DocxModal - Novo método
    Route::get('/sgc/contratada/get_docx/{itemId}/{contratoId}/{versao}/{numRelatorio}', [RelatorioCoordenacaoController::class, 'getDocx'])->name('sgc.contratada.get_docx');

    // Módulo de edição
    Route::get('sgc/edicao',                                 [EmpreendimentosController::class,                       'editavel'])->name('sgc.contratada.edicao');
    Route::get('sgc/edicao-estudos',                         [EmpreendimentosController::class,                       'editavelestudos'])->name('sgc.contratada.edicaoestudos');
    Route::get('sgc/edicao-produtos',                        [EmpreendimentosController::class,                       'editavelprodutos'])->name('sgc.contratada.edicaoprodutos');

    Route::post('/updatecampo/{corretor}',                   [EmpreendimentosController::class,                       'updatecampo'])->name('sgc.contratada.updatecampo');
    Route::post('/updatecampoestudos/{corretor}',            [EmpreendimentosController::class,                       'updatecampoestudos'])->name('sgc.contratada.updatecampoestudos');
    Route::post('/updatecampoprodutos/{corretor}',           [EmpreendimentosController::class,                       'updatecampoprodutos'])->name('sgc.contratada.updatecampoprodutos');

    Route::get('{tipo}/empreendimentos-export',              [EmpreendimentosController::class,                       'export'])->name('sgc.contratada.empreendimentos.export');
    Route::get('{tipo}/estudos-export',                      [EmpreendimentosController::class,                       'estudosexport'])->name('sgc.contratada.estudos.export');
    Route::get('{tipo}/subprodutos-export',                  [EmpreendimentosController::class,                       'subprodutosexport'])->name('sgc.contratada.subprodutos.export');
    Route::post('/cadastrarempreendimento/{corretor}',       [EmpreendimentosController::class,                       'cadastrarempreendimento'])->name('sgc.contratada.cadastrarempreendimento');
    Route::post('/cadastrarestudo/{corretor}',               [EmpreendimentosController::class,                       'cadastrarestudo'])->name('sgc.contratada.cadastrarestudo');
    Route::post('/cadastrarsubproduto/{corretor}',           [EmpreendimentosController::class,                       'cadastrarsubproduto'])->name('sgc.contratada.cadastrarsubproduto');

    // ABIO
    Route::post('produtos/{produto}/abio', [StoreProdutoAbioController::class, 'store'])->name('sgc.contratada.produtos.abio.store');
    Route::delete('produtos/{produto}/abio/{produto_abio}', [StoreProdutoAbioController::class, 'destroy'])->name('sgc.contratada.produtos.abio.delete');

    Route::get('produtos/fauna/modelos/download', [CampanhaController::class, 'downloadModelos'])
    ->middleware('auth')
    ->name('sgc.contratada.produtos.fauna.modelos.download');
    
    // PREFIX COM MIDDLEWARE route-permission
    Route::prefix('{contrato}/produtos/{produto}')->middleware(['auth'])->group(function () {
        Route::get('/', [ProdutosController::class, 'index'])->name('sgc.contratada.produtos.index');
        Route::get('create', [ProdutosController::class, 'create'])->name('sgc.contratada.produtos.create');
        Route::post('/', [ProdutosController::class, 'store'])->name('sgc.contratada.produtos.store');
        Route::post('abio/store', [StoreProdutoAbioController::class, 'store'])->name('sgc.contratada.produtos.abio.store');
        Route::delete('abio/{produto_abio}', [StoreProdutoAbioController::class, 'destroy'])->name('sgc.contratada.produtos.abio.destroy');

        // Draft progressivo
        Route::post('rascunho/inicializar', InitializarRascunhoCampanhaController::class)->name('sgc.contratada.produtos.rascunho.inicializar');
        Route::post('rascunho/{campanhaId}/etapa/{etapa}', SalvarEtapaCampanhaController::class)->name('sgc.contratada.produtos.rascunho.etapa');
        Route::post('rascunho/{campanhaId}/submeter', SubmeterCampanhaController::class)->name('sgc.contratada.produtos.rascunho.submeter');

        Route::get('fauna/atropelamento/modelo-planilha', [CampanhaController::class, 'downloadModeloAtropelamento'])->name('sgc.contratada.produtos.fauna.atropelamento.modelo');
        Route::post('profissional/store', [ProfissionalController::class, 'storeProfissional'])->name('sgc.contratada.produtos.profissional.store');
        // Route::get('campanhas/{campanhaId}', [CampanhaController::class, 'show'])->name('sgc.contratada.produtos.show');
        Route::get('campanhas/{campanhaId}/{modulo?}', [CampanhaController::class, 'show'])->name('sgc.contratada.produtos.show');
        Route::get('fauna/campanhas/{campanha}/analise', [CampanhaController::class, 'analise'])->name('sgc.contratada.produtos.analise');
        Route::post('fauna/campanhas/{campanha}/analise', [CampanhaController::class, 'salvarAnalise'])->name('sgc.contratada.produtos.salvarAnalise');
                
        // Aprovar ou Reprovar tudo
        Route::post('fauna/campanhas/{campanha}/aprovar-tudo', [CampanhaController::class, 'aprovarTudo'])->name('sgc.contratada.produtos.aprovarTudo');
        Route::post('fauna/campanhas/{campanha}/reprovar-tudo', [CampanhaController::class, 'reprovarTudo'])->name('sgc.contratada.produtos.reprovarTudo');
        
        Route::get('campanha/{campanha}/edit', [CampanhaController::class, 'edit'])->name('sgc.contratada.produtos.edit');
        Route::post('campanha/{campanha}/update', [CampanhaController::class, 'update'])->name('sgc.contratada.produtos.update');

        Route::delete('campanha/{campaignId}/profissional/{profissionalVinculoId}', [CampanhaController::class, 'deleteProfissional'])->name('sgc.contratada.produtos.profissional.destroy');

        Route::post('campanha/{campanha}/comentario', [ComentarioController::class, 'salvarComentario'])->name('sgc.contratada.produtos.comentario');
        Route::delete('campanha/{campanha}/comentario/{comentario}', [ComentarioController::class, 'destroyComentario'])->name('sgc.contratada.produtos.comentario.destroy');
        Route::delete('campanha/{campanha}/anexo/{anexoId}', [AnexoController::class, 'destroyAnexo'])->name('sgc.contratada.produtos.anexo.destroy');
        Route::delete('campanha/{campanhaId}/destroy', DestroyCampanhaController::class)->name('sgc.contratada.produtos.destroy');

        Route::post('campanha/{campanha}/arquivar', [CampanhaController::class, 'arquivar'])->name('sgc.contratada.produtos.arquivar');
        Route::post('campanha/{campanha}/restaurar', [CampanhaController::class, 'restaurar'])->name('sgc.contratada.produtos.restaurar');
        
        Route::post('/campanhas/{campanha}/finalizar-avaliacao', [CampanhaController::class, 'finalizarAvaliacao'])->name('sgc.contratada.produtos.finalizarAvaliacao');

        // Grupo específico para Espeleologia
        Route::prefix('espeleologia')->group(function () {
            Route::post('salvar-campanha', [EspeleoCampanhaController::class, 'salvarCampanha'])->name('sgc.contratada.produtos.espeleo.salvar_campanha');
            Route::post('profissional/store', [EspeleoCampanhaController::class, 'storeProfissional'])->name('sgc.contratada.produtos.espeleo.profissional.store');
            Route::get('profissionais', [EspeleoCampanhaController::class, 'getProfissionais'])->name('sgc.contratada.produtos.espeleo.profissionais');
            Route::get('campanhas/{campanhaId}', [EspeleoCampanhaController::class, 'show'])->name('sgc.contratada.produtos.espeleo.show');
            Route::post('campanhas/{campanhaId}/approve', [EspeleoCampanhaController::class, 'approve'])->name('sgc.contratada.produtos.espeleo.approve')->middleware('auth', 'role:analista');
            Route::post('resultados/upload', [EspeleoCampanhaController::class, 'uploadResultadoAnexo'])->name('sgc.contratada.produtos.espeleo.resultados.upload');
            Route::post('planilha-feicoes/upload', [EspeleoCampanhaController::class, 'uploadPlanilhaFeicoes'])->name('sgc.contratada.produtos.espeleo.planilha_feicoes.upload');
            Route::post('resultados/{id}/update', [EspeleoCampanhaController::class, 'updateResultadoAnexo'])->name('sgc.contratada.produtos.espeleo.resultados.update');
            Route::delete('resultados/{id}/delete', [EspeleoCampanhaController::class, 'deleteResultadoAnexo'])->name('sgc.contratada.produtos.espeleo.resultados.delete');
            Route::post('anexos/upload', [EspeleoCampanhaController::class, 'uploadAnexo'])->name('sgc.contratada.produtos.espeleo.anexos.upload');
            Route::post('anexos/{id}/update', [EspeleoCampanhaController::class, 'updateAnexo'])->name('sgc.contratada.produtos.espeleo.anexos.update');
            Route::delete('anexos/{id}/delete', [EspeleoCampanhaController::class, 'deleteAnexo'])->name('sgc.contratada.produtos.espeleo.anexos.delete');
            Route::post('estudos/store', [EspeleoCampanhaController::class, 'storeEstudosPosteriores'])->name('sgc.contratada.produtos.espeleo.estudos.store');
        });

        Route::patch('/pmqa-update',                                      [ProdutosController::class,                              'updatePmqa'])->name('sgc.contratada.produtos.pmqa.update');
        Route::patch('/pmqa/{pmqa}/aprovar',                              [ProdutosController::class,                              'aprovarPmqa'])->name('sgc.contratada.produtos.pmqa.aprovar');

        Route::prefix('/recursos-pmqa')->group(function () {
            require __DIR__ . '/../../Contratada/Produtos/PMQA/Configuracao/Ponto/Routes/PontoRoutes.php';
            require __DIR__ . '/../../Contratada/Produtos/PMQA/Configuracao/Parametro/Routes/ParametroRoutes.php';
            require __DIR__ . '/../../Contratada/Produtos/PMQA/Configuracao/VinculacaoPonto/Routes/VinculacaoPontoRoutes.php';
            require __DIR__ . '/../../Contratada/Produtos/PMQA/Execucao/app/Routes/ExecucaoRoutes.php';
            require __DIR__ . '/../../Contratada/Produtos/PMQA/Resultado/app/Routes/ResultadoRoutes.php';
            require __DIR__ . '/../../Contratada/Produtos/PMQA/Relatorio/app/Routes/RelatorioRoutes.php';

        });
    });

        /**
     * CONFERIR NOVAMENTE E CAPTURAR IMPRESSÕES DE VIEW DO DOCUMENTO
     * ESTABELECER ROTA ESPECÍFICA PRA ESPELEOLOGIA
     *
     */

        //  Route::post('/{contrato}/produtos/{produto}/resultados/upload', [ResultadoController::class, 'upload'])->name('sgc.contratada.produtos.fauna.resultados.upload');

    // Rotas para MapLayerController
    // routes/web.php ou routes/api.php

        Route::get('/mapa/wms', [MapLayerController::class, 'proxyWms']);

        Route::post('/espeleologia/layers/upload-shapefile', [MapLayerController::class, 'store'])->name('sgc.contratada.espeleologia.layers.upload_shapefile');
        Route::post('/espeleologia/layers/{layer}/publish', [MapLayerController::class, 'publish'])->name('sgc.contratada.espeleologia.layers.publish');
        Route::get('/espeleologia/layers', [MapLayerController::class, 'index'])->name('sgc.contratada.espeleologia.layers.index');
        Route::delete('/espeleologia/layers/{layer}/desvincular', [MapLayerController::class, 'desvincular'])->name('sgc.contratada.espeleologia.layers.desvincular');
        // Rotas para MapPageController
        Route::get('/espeleologia/mapa/viewer', [MapPageController::class, 'viewer'])->name('sgc.contratada.espeleologia.mapa.viewer');
        Route::get('/espeleologia/mapa/create', [MapPageController::class, 'create'])->name('sgc.contratada.espeleologia.mapa.create');




});