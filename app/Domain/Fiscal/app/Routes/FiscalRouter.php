<?php

use App\Domain\Fiscal\app\Controllers\IndexFiscalController;
use App\Domain\Fiscal\app\Controllers\ListagemConfiguracoesPMQAController;
use App\Domain\Fiscal\app\Controllers\ListagemServicosController;
use Illuminate\Support\Facades\Route;

Route::prefix('fiscal')->group(function () {
    Route::get('/{tipo}', [IndexFiscalController::class, 'index'])->name('fiscal.index');

    Route::prefix('/')->group(function () {
        Route::get('{contrato}/servicos', [ListagemServicosController::class, 'index'])->name('fiscal.dados.servicos.index');
        Route::get('{contrato}/configuracoes/pqma', [ListagemConfiguracoesPMQAController::class, 'index'])->name('fiscal.configuracoes.pmqa.index');
    });

    require __DIR__ . '/../../Parecer/Routes/ParecerRouter.php';

});
