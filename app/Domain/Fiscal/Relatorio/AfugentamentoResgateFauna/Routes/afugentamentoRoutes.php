<?php

use App\Domain\Fiscal\Relatorio\AfugentamentoResgateFauna\Controller\IndexController;
use App\Domain\Fiscal\Relatorio\AfugentamentoResgateFauna\Controller\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/', [IndexController::class, 'index'])->name('fiscal.relatorio.afugentamento.index');
Route::post('{contrato}/store', [StoreController::class, 'index'])->name('fiscal.relatorio.afugentamento.store');
