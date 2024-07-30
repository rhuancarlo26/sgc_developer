<?php


use App\Domain\Servico\SupressaoVegetacao\Configuracao\PatioEstocagem\Controller\IndexController;
use App\Domain\Servico\SupressaoVegetacao\Configuracao\PatioEstocagem\Controller\StoreController;
use Illuminate\Support\Facades\Route;

Route::prefix('/patio-estocagem')->group(function () {
    Route::get('/{contrato}/{servico}', IndexController::class)->name('contratos.contratada.servicos.supressao-vegetacao.configuracao.patio-estocagem.index');
    Route::post('/store',               StoreController::class)->name('contratos.contratada.servicos.supressao-vegetacao.configuracao.patio-estocagem.store');
});
