<?php

use App\Domain\Servico\SupressaoVegetacao\Relatorio\Controller\DeleteAnexoController;
use App\Domain\Servico\SupressaoVegetacao\Relatorio\Controller\DeleteController;
use App\Domain\Servico\SupressaoVegetacao\Relatorio\Controller\IndexController;
use App\Domain\Servico\SupressaoVegetacao\Relatorio\Controller\RelatorioController;
use App\Domain\Servico\SupressaoVegetacao\Relatorio\Controller\StoreAnexoController;
use App\Domain\Servico\SupressaoVegetacao\Relatorio\Controller\StoreController;
use App\Domain\Servico\SupressaoVegetacao\Relatorio\Controller\UpdateController;
use Illuminate\Support\Facades\Route;

Route::get('/{contrato}/{servico}',                              IndexController::class)->name('contratos.contratada.servicos.supressao-vegetacao.relatorio.index');
Route::get('/relatorio',                                         RelatorioController::class)->name('contratos.contratada.servicos.supressao-vegetacao.relatorio.get-relatorio');
Route::post('/store',                                            StoreController::class)->name('contratos.contratada.servicos.supressao-vegetacao.relatorio.store');
Route::post('/store-anexo',                                      StoreAnexoController::class)->name('contratos.contratada.servicos.supressao-vegetacao.relatorio.relatorio-anexo.store');
Route::delete('/delete-anexo/{anexo}',                           DeleteAnexoController::class)->name('contratos.contratada.servicos.supressao-vegetacao.relatorio.relatorio-anexo.delete');
Route::patch('/update',                                          UpdateController::class)->name('contratos.contratada.servicos.supressao-vegetacao.relatorio.update');
Route::delete('{relatorio}/delete',                              DeleteController::class)->name('contratos.contratada.servicos.supressao-vegetacao.relatorio.delete');
