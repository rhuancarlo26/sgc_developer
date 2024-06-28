<?php

use App\Domain\Servico\PMQA\Execucao\Coleta\app\Controller\StoreController;
use App\Domain\Servico\PMQA\Execucao\Coleta\app\Controller\UpdateController;
use Illuminate\Support\Facades\Route;

Route::prefix('/coleta')->group(function () {
  Route::post('{contrato}/{servico}/{campanha}/store',    [StoreController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.coleta.store');
  Route::patch('{contrato}/{servico}/{campanha}/update',  [UpdateController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.coleta.update');
});