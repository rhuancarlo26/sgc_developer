<?php

use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Controller\CreateController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Controller\DeleteController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Controller\DownloadModeloController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Controller\ImportarController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Controller\IndexController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Controller\UpdateController;
use Illuminate\Support\Facades\Route;

Route::prefix('ponto')->group(function () {
  Route::get('{pmqa}',                    [IndexController::class,          'index'])->name('contratos.contratada.sgc.pmqa.configuracao.ponto.index');
  Route::get('{pmqa}/create/{ponto}',     [CreateController::class,         'index'])->name('contratos.contratada.sgc.pmqa.configuracao.ponto.create');
  Route::get('download_modelo',           [DownloadModeloController::class, 'index'])->name('contratos.contratada.sgc.pmqa.configuracao.ponto.download_modelo');
  Route::post('{pmqa}/importar',          [ImportarController::class,       'index'])->name('contratos.contratada.sgc.pmqa.configuracao.ponto.importar');
  Route::patch('{pmqa}/update',           [UpdateController::class,         'index'])->name('contratos.contratada.sgc.pmqa.configuracao.ponto.update');
  Route::delete('{pmqa}/delete/{ponto}',  [DeleteController::class,         'index'])->name('contratos.contratada.sgc.pmqa.configuracao.ponto.delete');
});
