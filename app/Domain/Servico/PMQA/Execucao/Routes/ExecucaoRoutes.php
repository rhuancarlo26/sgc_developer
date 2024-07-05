<?php

use App\Domain\Servico\PMQA\Execucao\Controller\DestroyController;
use App\Domain\Servico\PMQA\Execucao\Controller\GerenciarController;
use App\Domain\Servico\PMQA\Execucao\Controller\GetPontosCampanhaController;
use App\Domain\Servico\PMQA\Execucao\Controller\IndexController;
use App\Domain\Servico\PMQA\Execucao\Controller\StoreController;
use App\Domain\Servico\PMQA\Execucao\Controller\UpdateController;
use Illuminate\Support\Facades\Route;

Route::prefix('/execucao')->group(function () {
  Route::get('{contrato}/{servico}/',                               [IndexController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.index');
  Route::get('{contrato}/{servico}/gerenciar/{campanha}',           [GerenciarController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.gerenciar');
  Route::post('{contrato}/{servico}/store',                         [StoreController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.store');
  Route::patch('{contrato}/{servico}/update',                       [UpdateController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.update');
  Route::delete('{contrato}/{servico}/destroy/{campanha}',          [DestroyController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.destroy');
});