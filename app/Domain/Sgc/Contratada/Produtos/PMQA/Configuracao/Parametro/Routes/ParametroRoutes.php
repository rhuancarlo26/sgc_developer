<?php

use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Parametro\Controller\DestroyController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Parametro\Controller\IndexController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Parametro\Controller\StoreController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Parametro\Controller\UpdateController;
use Illuminate\Support\Facades\Route;

Route::prefix('/parametro')->group(function () {
  Route::get('{servico}/',                   [IndexController::class, 'index'])->name('contratos.contratada.servicos.pmqa.configuracao.parametro.index');
  Route::post('{servico}/store',             [StoreController::class, 'index'])->name('contratos.contratada.servicos.pmqa.configuracao.parametro.store');
  Route::patch('{servico}/update',           [UpdateController::class, 'index'])->name('contratos.contratada.servicos.pmqa.configuracao.parametro.update');
  Route::delete('{servico}/destroy/{lista}', [DestroyController::class, 'index'])->name('contratos.contratada.servicos.pmqa.configuracao.parametro.destroy');
});
