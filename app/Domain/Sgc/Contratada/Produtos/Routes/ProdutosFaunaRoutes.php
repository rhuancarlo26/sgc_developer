<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Sgc\Contratada\Produtos\Controller\ProdutosController;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Controller\AnexoController;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Controller\CampanhaController;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Controller\ComentarioController;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Controller\ProfissionalController;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Controller\FaunaSimplificadaAnaliseController;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Controller\DestroyCampanhaController;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Controller\InitializarRascunhoCampanhaController;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Controller\SalvarEtapaCampanhaController;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Controller\SubmeterCampanhaController;
use App\Domain\Sgc\Contratada\Modulo\ConfigPlanilha\Controllers\ConfiguracoesModulosController;
use App\Domain\Sgc\Contratada\Modulo\ConfigPlanilha\Controllers\CreateConfigModuloController;
use App\Domain\Sgc\Contratada\Modulo\ConfigPlanilha\Controllers\StoreConfigModuloController;
use App\Domain\Sgc\Contratada\Modulo\ConfigPlanilha\Controllers\UpdateConfigModuloController;
use App\Domain\Sgc\Contratada\Modulo\ConfigPlanilha\Controllers\ProcessarCamposPlanilhaController;
use App\Domain\Sgc\Contratada\Modulo\ConfigPlanilha\Controllers\GerarPlanilhaModeloController;
use App\Domain\Sgc\Contratada\Modulo\ConfigPlanilha\Controllers\DeleteConfigModuloController;

    Route::get('produtos/fauna/modelos/download', [CampanhaController::class, 'downloadModelos'])
    ->middleware('auth')
    ->name('sgc.contratada.produtos.fauna.modelos.download');

    // PREFIX COM MIDDLEWARE route-permission
    Route::prefix('{contrato}/produtos/{produto}')->middleware(['auth', \App\Domain\Sgc\Contratada\Produtos\Middleware\EnsureFaunaProduct::class])->group(function () {
        Route::get('/', [ProdutosController::class, 'index'])->name('sgc.contratada.produtos.index');
        Route::get('create', [ProdutosController::class, 'create'])->name('sgc.contratada.produtos.create');
        Route::post('/', [ProdutosController::class, 'store'])->name('sgc.contratada.produtos.store');
        Route::post('fauna/simplificada/anexos/upload', [FaunaSimplificadaAnaliseController::class, 'uploadAnexo'])
            ->withoutMiddleware('route-permission')
            ->middleware('throttle:120,1')
            ->name('sgc.contratada.produtos.fauna.simplificada.anexos.upload');

        Route::get('fauna/campanhas/{campanha}/simplificada', [FaunaSimplificadaAnaliseController::class, 'show'])->name('sgc.contratada.produtos.fauna.simplificada.show');
        Route::get('fauna/campanhas/{campanha}/simplificada/edit', [FaunaSimplificadaAnaliseController::class, 'edit'])->name('sgc.contratada.produtos.fauna.simplificada.edit');
        Route::post('fauna/campanhas/{campanha}/simplificada', [FaunaSimplificadaAnaliseController::class, 'update'])->name('sgc.contratada.produtos.fauna.simplificada.update');
        Route::get('fauna/campanhas/{campanha}/simplificada/analise', [FaunaSimplificadaAnaliseController::class, 'analise'])->name('sgc.contratada.produtos.fauna.simplificada.analise');
        Route::post('fauna/campanhas/{campanha}/simplificada/aprovar', [FaunaSimplificadaAnaliseController::class, 'aprovar'])->name('sgc.contratada.produtos.fauna.simplificada.aprovar');
        Route::post('fauna/campanhas/{campanha}/simplificada/reprovar', [FaunaSimplificadaAnaliseController::class, 'reprovar'])->name('sgc.contratada.produtos.fauna.simplificada.reprovar');

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

        Route::prefix('modulos/configuracoes')
            ->name('sgc.contratada.produtos.modulos.configuracoes.')
            ->group(function () {
                Route::get('/', [ConfiguracoesModulosController::class, 'index'])->name('index');
                Route::get('/formulario/{modulo?}', [CreateConfigModuloController::class, 'index'])->name('formulario');
                Route::post('/processar-campos-planilha', [ProcessarCamposPlanilhaController::class, 'processarCamposPlanilha'])->name('processar-campos-planilha');
                Route::post('/formulario', [StoreConfigModuloController::class, 'store'])->name('store');
                Route::post('/formulario/atualizar/{modulo?}', [UpdateConfigModuloController::class, 'update'])->name('update');
                Route::delete('/formulario/excluir/{modulo?}', [DeleteConfigModuloController::class, 'delete'])->name('delete');
                Route::get('/gerar-planilha-modelo/{modulo}', [GerarPlanilhaModeloController::class, 'gerarPlanilhaModelo'])->name('gerar-planilha-modelo');
        });

    });
