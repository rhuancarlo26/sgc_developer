<?php

use App\Domain\Servico\PMQA\Configuracao\Ponto\Controller\CreateController;
use App\Domain\Servico\PMQA\Configuracao\Ponto\Controller\DeleteController;
use App\Domain\Servico\PMQA\Configuracao\Ponto\Controller\DownloadModeloController;
use App\Domain\Servico\PMQA\Configuracao\Ponto\Controller\ImportarController;
use App\Domain\Servico\PMQA\Configuracao\Ponto\Controller\IndexController;
use App\Domain\Servico\PMQA\Configuracao\Ponto\Controller\UpdateController;
use Illuminate\Support\Facades\Route;

Route::prefix('/ponto')->group(function () {
  Route::get('{contrato}/{servico}/',                   [IndexController::class,          'index'])->name('contratos.contratada.servicos.pmqa.configuracao.ponto.index');
  Route::get('{contrato}/{servico}/create/{ponto}',     [CreateController::class,         'index'])->name('contratos.contratada.servicos.pmqa.configuracao.ponto.create');
  Route::get('download_modelo',                         [DownloadModeloController::class, 'index'])->name('contratos.contratada.servicos.pmqa.configuracao.ponto.download_modelo');
  Route::post('{contrato}/{servico}/importar',          [ImportarController::class,       'index'])->name('contratos.contratada.servicos.pmqa.configuracao.ponto.importar');
  Route::patch('{contrato}/{servico}/update',           [UpdateController::class,         'index'])->name('contratos.contratada.servicos.pmqa.configuracao.ponto.update');
  Route::delete('{contrato}/{servico}/delete/{ponto}',  [DeleteController::class,         'index'])->name('contratos.contratada.servicos.pmqa.configuracao.ponto.delete');
});
