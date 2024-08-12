<?php

use App\Domain\Fiscal\app\Controllers\IndexFiscalController;
use App\Domain\Fiscal\app\Controllers\ListagemConfiguracoesPMQAController;
use App\Domain\Fiscal\app\Controllers\ListagemServicosController;
use App\Domain\Fiscal\app\Controllers\ListagemConfiguracoesSupressaoVegetacaoController;
use App\Domain\Fiscal\app\Controllers\ListagemConfiguracoesAfugentamentoController;
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
    });

    require __DIR__ . '/../../Parecer/Routes/ParecerRouter.php';

});
