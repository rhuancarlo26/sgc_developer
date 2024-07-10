<?php

use App\Domain\Servico\PMQA\Resultado\app\Controller\IndexController;
use App\Domain\Servico\PMQA\Resultado\app\Controller\StoreController;
use App\Domain\Servico\PMQA\Resultado\app\Controller\UpdateController;
use App\Domain\Servico\PMQA\Resultado\app\Controller\DeleteController;
use App\Domain\Servico\PMQA\Resultado\app\Controller\ResultadoController;
use App\Domain\Servico\PMQA\Resultado\app\Controller\StoreAnaliseController;
use App\Domain\Servico\PMQA\Resultado\app\Controller\UpdateAnaliseController;
use Illuminate\Support\Facades\Route;

Route::prefix('/resultado')->group(function () {
  Route::get('{contrato}/{servico}/',                             [IndexController::class, 'index'])->name('contratos.contratada.servicos.pmqa.resultado.index');
  Route::post('{contrato}/{servico}/store',                       [StoreController::class, 'index'])->name('contratos.contratada.servicos.pmqa.resultado.store');
  Route::patch('{contrato}/{servico}/update',                     [UpdateController::class, 'index'])->name('contratos.contratada.servicos.pmqa.resultado.update');
  Route::delete('{contrato}/{servico}/delete/{resultado}',        [DeleteController::class, 'index'])->name('contratos.contratada.servicos.pmqa.resultado.delete');
  Route::get('{contrato}/{servico}/resultado/{resultado}',        [ResultadoController::class, 'index'])->name('contratos.contratada.servicos.pmqa.resultado.resultado');
  Route::post('{contrato}/{servico}/{resultado}/store_analise',   [StoreAnaliseController::class, 'index'])->name('contratos.contratada.servicos.pmqa.resultado.store_analise');
  Route::patch('{contrato}/{servico}/{resultado}/update_analise', [UpdateAnaliseController::class, 'index'])->name('contratos.contratada.servicos.pmqa.resultado.update_analise');
});