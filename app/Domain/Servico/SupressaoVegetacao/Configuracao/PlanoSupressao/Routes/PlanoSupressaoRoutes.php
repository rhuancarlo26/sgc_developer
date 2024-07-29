<?php

use App\Domain\Servico\SupressaoVegetacao\Configuracao\PlanoSupressao\Controller\IndexController;
use App\Domain\Servico\SupressaoVegetacao\Configuracao\PlanoSupressao\Controller\StoreController;
use Illuminate\Support\Facades\Route;

Route::prefix('/plano-supressao')->group(function () {
    Route::get('/{contrato}/{servico}', IndexController::class)->name('contratos.contratada.servicos.supressao-vegetacao.configuracao.plano-supressao.index');
    Route::post('/store',               StoreController::class)->name('contratos.contratada.servicos.supressao-vegetacao.configuracao.plano-supressao.store');
});
