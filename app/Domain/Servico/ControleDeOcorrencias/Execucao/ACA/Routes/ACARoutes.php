<?php

use Illuminate\Support\Facades\Route;

use App\Domain\Servico\ControleDeOcorrencias\Execucao\ACA\Controller\IndexController;

Route::prefix('/aca')->group(function () {
  Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.controledeocorrencias.execucao.aca.index');
});
