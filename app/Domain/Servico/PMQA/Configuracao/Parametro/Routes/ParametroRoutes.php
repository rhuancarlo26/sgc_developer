<?php

use App\Domain\Servico\PMQA\Configuracao\Parametro\Controller\IndexController;
use App\Domain\Servico\PMQA\Configuracao\Parametro\Controller\StoreController;
use Illuminate\Support\Facades\Route;

Route::prefix('/parametro')->group(function () {
  Route::get('{contrato}/{servico}/',       [IndexController::class, 'index'])->name('contratos.contratada.servicos.pmqa.configuracao.parametro.index');
  Route::post('{contrato}/{servico}/store',  [StoreController::class, 'index'])->name('contratos.contratada.servicos.pmqa.configuracao.parametro.store');
});
