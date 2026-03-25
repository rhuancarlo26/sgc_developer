<?php

use App\Domain\Fiscal\Relatorio\PassagemFauna\Controller\IndexController;
use App\Domain\Fiscal\Relatorio\PassagemFauna\Controller\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/', [IndexController::class, 'index'])->name('fiscal.relatorio.passagem_fauna.index');
Route::post('{contrato}/store', [StoreController::class, 'index'])->name('fiscal.relatorio.passagem_fauna.store');
