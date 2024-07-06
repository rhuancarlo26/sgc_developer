<?php

use App\Domain\Servico\PMQA\Resultado\app\Controller\IndexController;
use App\Domain\Servico\PMQA\Resultado\app\Controller\StoreController;
use App\Domain\Servico\PMQA\Resultado\app\Controller\UpdateController;
use App\Domain\Servico\PMQA\Resultado\app\Controller\DeleteController;
use Illuminate\Support\Facades\Route;

Route::prefix('/resultado')->group(function () {
  Route::get('{contrato}/{servico}/',                       [IndexController::class, 'index'])->name('contratos.contratada.servicos.pmqa.resultado.index');
  Route::post('{contrato}/{servico}/store',                 [StoreController::class, 'index'])->name('contratos.contratada.servicos.pmqa.resultado.store');
  Route::patch('{contrato}/{servico}/update',               [UpdateController::class, 'index'])->name('contratos.contratada.servicos.pmqa.resultado.update');
  Route::delete('{contrato}/{servico}/delete/{resultado}',  [DeleteController::class, 'index'])->name('contratos.contratada.servicos.pmqa.resultado.delete');
});