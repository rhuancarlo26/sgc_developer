<?php

use App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Controller\IndexController;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Controller\StoreController;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Controller\UpdateController;
use Illuminate\Support\Facades\Route;

Route::prefix('/supressao')->group(function () {
    Route::get('/{contrato}/{servico}', IndexController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.supressao.index');
    Route::post('/store',               StoreController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.supressao.store');
    Route::patch('/update',             UpdateController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.supressao.update');
});
