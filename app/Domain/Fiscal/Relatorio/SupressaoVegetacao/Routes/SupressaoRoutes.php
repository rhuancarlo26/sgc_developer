<?php

use App\Domain\Fiscal\Relatorio\SupressaoVegetacao\Controller\IndexController;
use App\Domain\Fiscal\Relatorio\SupressaoVegetacao\Controller\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/', [IndexController::class, 'index'])->name('fiscal.relatorio.supressao_vegetacao.index');
Route::post('{contrato}/store', [StoreController::class, 'index'])->name('fiscal.relatorio.supressao_vegetacao.store');
