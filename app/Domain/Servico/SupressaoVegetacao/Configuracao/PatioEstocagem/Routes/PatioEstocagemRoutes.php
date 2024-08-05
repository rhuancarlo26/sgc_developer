<?php


use App\Domain\Servico\SupressaoVegetacao\Configuracao\PatioEstocagem\Controller\DeleteController;
use App\Domain\Servico\SupressaoVegetacao\Configuracao\PatioEstocagem\Controller\DeleteFotoController;
use App\Domain\Servico\SupressaoVegetacao\Configuracao\PatioEstocagem\Controller\IndexController;
use App\Domain\Servico\SupressaoVegetacao\Configuracao\PatioEstocagem\Controller\StoreController;
use App\Domain\Servico\SupressaoVegetacao\Configuracao\PatioEstocagem\Controller\UpdateController;
use Illuminate\Support\Facades\Route;

Route::prefix('/patio-estocagem')->group(function () {
    Route::get('/{contrato}/{servico}',              IndexController::class)->name('contratos.contratada.servicos.supressao-vegetacao.configuracao.patio-estocagem.index');
    Route::post('/store',                            StoreController::class)->name('contratos.contratada.servicos.supressao-vegetacao.configuracao.patio-estocagem.store');
    Route::patch('{patio}/update',                   UpdateController::class)->name('contratos.contratada.servicos.supressao-vegetacao.configuracao.patio-estocagem.update');
    Route::delete('{patio}/deletar',                 DeleteController::class)->name('contratos.contratada.servicos.supressao-vegetacao.configuracao.patio-estocagem.delete');
    Route::delete('fotos/{arquivo}/{patio}/deletar', DeleteFotoController::class)->name('contratos.contratada.servicos.supressao-vegetacao.configuracao.patio-estocagem.fotos.delete');
});
