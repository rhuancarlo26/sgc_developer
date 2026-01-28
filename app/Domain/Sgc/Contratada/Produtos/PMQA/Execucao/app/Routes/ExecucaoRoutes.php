<?php

use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\app\Controller\DestroyController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\app\Controller\GerenciarController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\app\Controller\IndexController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\app\Controller\StoreController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\app\Controller\UpdateController;
use Illuminate\Support\Facades\Route;

Route::prefix('/execucao')->group(function () {
  Route::get('{pmqa}/',                               [IndexController::class, 'index'])->name('contratos.contratada.sgc.pmqa.execucao.index');
  Route::get('{pmqa}/gerenciar/{campanha}',           [GerenciarController::class, 'index'])->name('contratos.contratada.sgc.pmqa.execucao.gerenciar');
  Route::post('{pmqa}/store',                         [StoreController::class, 'index'])->name('contratos.contratada.sgc.pmqa.execucao.store');
  Route::patch('{pmqa}/update',                       [UpdateController::class, 'index'])->name('contratos.contratada.sgc.pmqa.execucao.update');
  Route::delete('{pmqa}/destroy/{campanha}',          [DestroyController::class, 'index'])->name('contratos.contratada.sgc.pmqa.execucao.destroy');

  require __DIR__ . '/../../Coleta/app/Routes/ColetaRoutes.php';
  require __DIR__ . '/../../Medir/app/Routes/MedirRoutes.php';
});
