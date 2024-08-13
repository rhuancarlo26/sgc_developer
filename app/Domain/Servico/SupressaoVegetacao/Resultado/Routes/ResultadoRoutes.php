<?php

use App\Domain\Servico\SupressaoVegetacao\Resultado\Controller\DeleteController;
use App\Domain\Servico\SupressaoVegetacao\Resultado\Controller\IndexController;
use App\Domain\Servico\SupressaoVegetacao\Resultado\Controller\StoreController;
use App\Domain\Servico\SupressaoVegetacao\Resultado\Controller\UpdateController;
use Illuminate\Support\Facades\Route;

Route::get('/{contrato}/{servico}',             IndexController::class)->name('contratos.contratada.servicos.supressao-vegetacao.resultado.index');
Route::post('/store',                           StoreController::class)->name('contratos.contratada.servicos.supressao-vegetacao.resultado.store');
Route::patch('/update',                         UpdateController::class)->name('contratos.contratada.servicos.supressao-vegetacao.resultado.update');
Route::delete('{resultado}/delete',             DeleteController::class)->name('contratos.contratada.servicos.supressao-vegetacao.resultado.delete');

