<?php

use Illuminate\Support\Facades\Route;

use App\Domain\Servico\ControleDeOcorrencias\Execucao\Ocorrencia\Controller\IndexController;

Route::prefix('/ocorrencia')->group(function () {
  Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.controledeocorrencias.execucao.ocorrencia.index');
});
