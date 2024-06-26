<?php

use App\Domain\Servico\PMQA\Execucao\Controller\DestroyController;
use App\Domain\Servico\PMQA\Execucao\Controller\IndexController;
use App\Domain\Servico\PMQA\Execucao\Controller\StoreController;
use App\Domain\Servico\PMQA\Execucao\Controller\UpdateController;
use Illuminate\Support\Facades\Route;

Route::prefix('/execucao')->group(function () {
  Route::get('{contrato}/{servico}/',                       [IndexController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.index');
  Route::post('{contrato}/{servico}/store',                 [StoreController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.store');
  Route::patch('{contrato}/{servico}/update',               [UpdateController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.update');
  Route::delete('{contrato}/{servico}/destroy/{campanha}',  [DestroyController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.destroy');
});