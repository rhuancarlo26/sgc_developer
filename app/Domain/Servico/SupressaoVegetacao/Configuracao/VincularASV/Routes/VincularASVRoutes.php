<?php

use App\Domain\Servico\SupressaoVegetacao\Configuracao\VincularASV\Controller\DeleteController;
use App\Domain\Servico\SupressaoVegetacao\Configuracao\VincularASV\Controller\IndexController;
use App\Domain\Servico\SupressaoVegetacao\Configuracao\VincularASV\Controller\VincularASVController;
use Illuminate\Support\Facades\Route;

Route::prefix('/vincular-asv')->group(function () {
  Route::get('/{contrato}/{servico}',             IndexController::class)->name('contratos.contratada.servicos.supressao-vegetacao.configuracao.vincular-asv.index');
  Route::post('/vincular',                        VincularASVController::class)->name('contratos.contratada.servicos.supressao-vegetacao.configuracao.vincular-asv.vincular');
  Route::delete('/{contrato}/{servico}/{licenca}',DeleteController::class)->name('contratos.contratada.servicos.supressao-vegetacao.configuracao.vincular-asv.delete');
});
