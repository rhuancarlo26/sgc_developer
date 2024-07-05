<?php

use App\Domain\Servico\PMQA\Configuracao\VinculacaoPonto\Controller\DeleteController;
use App\Domain\Servico\PMQA\Configuracao\VinculacaoPonto\Controller\IndexController;
use App\Domain\Servico\PMQA\Configuracao\VinculacaoPonto\Controller\StoreController;
use Illuminate\Support\Facades\Route;

Route::prefix('/vinculacao_ponto')->group(function () {
  Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.pmqa.configuracao.vinculacao_ponto.index');
  Route::post('{contrato}/{servico}/store', [StoreController::class, 'index'])->name('contratos.contratada.servicos.pmqa.configuracao.vinculacao_ponto.store');
  Route::delete('{contrato}/{servico}/destroy/{lista}', [DeleteController::class, 'index'])->name('contratos.contratada.servicos.pmqa.configuracao.vinculacao_ponto.destroy');
});