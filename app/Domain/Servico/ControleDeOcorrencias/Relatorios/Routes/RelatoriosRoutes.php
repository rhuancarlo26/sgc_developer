<?php

use Illuminate\Support\Facades\Route;

use App\Domain\Servico\ControleDeOcorrencias\Relatorios\Controller\IndexController;

Route::prefix('/relatorios')->group(function () {
  Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.controledeocorrencias.relatorios.index');
});
