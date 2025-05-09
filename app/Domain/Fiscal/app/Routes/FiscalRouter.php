<?php

use App\Domain\Fiscal\app\Controllers\IndexFiscalController;
use App\Domain\Fiscal\app\Controllers\ListagemConfiguracoesPMQAController;
use App\Domain\Fiscal\app\Controllers\ListagemServicosController;
use App\Domain\Fiscal\app\Controllers\ListagemConfiguracoesSupressaoVegetacaoController;
use App\Domain\Fiscal\app\Controllers\ListagemConfiguracoesAfugentamentoController;
use App\Domain\Fiscal\app\Controllers\ListagemConfiguracoesAtropelamentoFaunaController;
use App\Domain\Fiscal\app\Controllers\ListagemConfiguracoesControleOcorrenciaController;
use App\Domain\Fiscal\app\Controllers\ListagemConfiguracoesPassagemFaunaController;
use App\Domain\Fiscal\app\Controllers\ParecerFiscalController;
use App\Domain\Fiscal\app\Controllers\TabRNCController;
use Illuminate\Support\Facades\Route;

Route::prefix('fiscal')->group(function () {
    Route::get('/{tipo}', [IndexFiscalController::class, 'index'])->name('fiscal.index');

    Route::prefix('/')->group(function () {
        Route::get('{contrato}/servicos', [ListagemServicosController::class, 'index'])
            ->name('fiscal.dados.servicos.index');

        Route::get('{contrato}/configuracoes/pqma', [ListagemConfiguracoesPMQAController::class, 'index'])
            ->name('fiscal.configuracoes.pmqa.index');

        Route::get('{contrato}/configuracoes/supressao', [ListagemConfiguracoesSupressaoVegetacaoController::class, 'index'])
            ->name('fiscal.configuracoes.supressao.index');

        Route::get('{contrato}/configuracoes/afugentamento', [ListagemConfiguracoesAfugentamentoController::class, 'index'])
            ->name('fiscal.configuracoes.afugentamento.index');

        Route::get('{contrato}/configuracoes/ocorrencia', [ListagemConfiguracoesControleOcorrenciaController::class, 'index'])
            ->name('fiscal.configuracoes.ocorrencia.index');

        Route::get('{contrato}/configuracoes/passagem_fauna', [ListagemConfiguracoesPassagemFaunaController::class, 'index'])
            ->name('fiscal.configuracoes.passagem_fauna.index');

        Route::get('{contrato}/configuracoes/atropelamento', [ListagemConfiguracoesAtropelamentoFaunaController::class, 'index'])
            ->name('fiscal.configuracoes.atropelamento.index');
    });

    Route::prefix('/rnc')->group(function () {
        Route::get('{contrato}/', [TabRNCController::class, 'index'])->name('fiscal.rnc.index');
        Route::post('{contrato}/parecer_fiscal', [ParecerFiscalController::class, 'index'])->name('fiscal.rnc.parecer_fiscal');
    });

    Route::prefix('/relatorio')->group(function () {
        require __DIR__ . "../../../Relatorio/app/Routes/RelatorioRouter.php";
    });

    require __DIR__ . '/../../Parecer/Routes/ParecerRouter.php';
});
