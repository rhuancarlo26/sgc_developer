<?php

use App\Domain\Servico\SupressaoVegetacao\Resultado\Controller\IndexController;
use App\Domain\Servico\SupressaoVegetacao\Resultado\Controller\StoreController;
use Illuminate\Support\Facades\Route;


Route::get('/{contrato}/{servico}',             IndexController::class)->name('contratos.contratada.servicos.supressao-vegetacao.resultado.index');
Route::post('/store',                           StoreController::class)->name('contratos.contratada.servicos.supressao-vegetacao.resultado.store');
//    Route::patch('/update',                         UpdateController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.supressao.update');
//    Route::delete('{area}/delete',                  DeleteController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.supressao.delete');

