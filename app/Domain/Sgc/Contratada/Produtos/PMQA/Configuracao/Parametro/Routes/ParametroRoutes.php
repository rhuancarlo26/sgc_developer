<?php

use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Parametro\Controller\DestroyController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Parametro\Controller\IndexController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Parametro\Controller\StoreController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Parametro\Controller\UpdateController;
use Illuminate\Support\Facades\Route;

Route::prefix('/parametro')->group(function () {
  Route::get('{pmqa}/',                   [IndexController::class, 'index'])->name('contratos.contratada.sgc.pmqa.configuracao.parametro.index');
  Route::post('{pmqa}/store',             [StoreController::class, 'index'])->name('contratos.contratada.sgc.pmqa.configuracao.parametro.store');
  Route::patch('{pmqa}/update',           [UpdateController::class, 'index'])->name('contratos.contratada.sgc.pmqa.configuracao.parametro.update');
  Route::delete('{pmqa}/destroy/{lista}', [DestroyController::class, 'index'])->name('contratos.contratada.sgc.pmqa.configuracao.parametro.destroy');
});
