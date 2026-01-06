<?php

use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\app\Controller\DestroyController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\app\Controller\GerenciarController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\app\Controller\IndexController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\app\Controller\StoreController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\app\Controller\UpdateController;
use Illuminate\Support\Facades\Route;

Route::prefix('/execucao')->group(function () {
  Route::get('{servico}/',                               [IndexController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.index');
  Route::get('{servico}/gerenciar/{campanha}',           [GerenciarController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.gerenciar');
  Route::post('{servico}/store',                         [StoreController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.store');
  Route::patch('{servico}/update',                       [UpdateController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.update');
  Route::delete('{servico}/destroy/{campanha}',          [DestroyController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.destroy');

  require __DIR__ . '/../../Coleta/app/Routes/ColetaRoutes.php';
  require __DIR__ . '/../../Medir/app/Routes/MedirRoutes.php';
});
