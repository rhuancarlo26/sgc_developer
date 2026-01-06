<?php

use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Controller\CreateController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Controller\DeleteController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Controller\DownloadModeloController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Controller\ImportarController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Controller\IndexController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Controller\UpdateController;
use Illuminate\Support\Facades\Route;

Route::prefix('/ponto')->group(function () {
  Route::get('{campanha}/',                   [IndexController::class,          'index'])->name('contratos.contratada.servicos.pmqa.configuracao.ponto.index');
  Route::get('{campanha}/create/{ponto}',     [CreateController::class,         'index'])->name('contratos.contratada.servicos.pmqa.configuracao.ponto.create');
  Route::get('download_modelo',                             [DownloadModeloController::class, 'index'])->name('contratos.contratada.servicos.pmqa.configuracao.ponto.download_modelo');
  Route::post('{campanha}/importar',          [ImportarController::class,       'index'])->name('contratos.contratada.servicos.pmqa.configuracao.ponto.importar');
  Route::patch('{campanha}/update',           [UpdateController::class,         'index'])->name('contratos.contratada.servicos.pmqa.configuracao.ponto.update');
  Route::delete('{campanha}/delete/{ponto}',  [DeleteController::class,         'index'])->name('contratos.contratada.servicos.pmqa.configuracao.ponto.delete');
});
