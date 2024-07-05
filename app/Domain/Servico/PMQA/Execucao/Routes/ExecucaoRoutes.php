<?php

use App\Domain\Servico\PMQA\Execucao\Controller\IndexController;
use Illuminate\Support\Facades\Route;

Route::prefix('/execucao')->group(function () {
  Route::get('{contrato}/{servico}/',                   [IndexController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.index');
});