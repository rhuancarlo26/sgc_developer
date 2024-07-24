<?php

use App\Domain\Fiscal\app\Controllers\IndexFiscalController;
use App\Domain\Fiscal\app\Controllers\TabConfiguracoesController;
use App\Domain\Fiscal\app\Controllers\TabRNCController;
use App\Domain\Fiscal\app\Controllers\TabServicosController;
use Illuminate\Support\Facades\Route;

Route::prefix('fiscal')->group(function () {
    Route::get('/{tipo}',                [IndexFiscalController::class,         'index'])->name('fiscal.index');

    Route::prefix('/')->group(function () {
        Route::get('{contrato}/servicos',       [TabServicosController::class, 'index'])->name('fiscal.dados.servicos.index');
        Route::get('{contrato}/configuracoes',  [TabConfiguracoesController::class, 'index'])->name('fiscal.dados.configuracoes.index');
        Route::get('{contrato}/rnc',            [TabRNCController::class, 'index'])->name('fiscal.dados.rnc.index');
    });
});
