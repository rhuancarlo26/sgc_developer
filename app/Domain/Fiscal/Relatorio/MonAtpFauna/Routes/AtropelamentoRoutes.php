<?php

use App\Domain\Fiscal\Relatorio\MonAtpFauna\Controller\IndexController;
use App\Domain\Fiscal\Relatorio\MonAtpFauna\Controller\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/', [IndexController::class, 'index'])->name('fiscal.relatorio.atropelamento.index');
Route::post('{contrato}/store', [StoreController::class, 'index'])->name('fiscal.relatorio.atropelamento.store');
