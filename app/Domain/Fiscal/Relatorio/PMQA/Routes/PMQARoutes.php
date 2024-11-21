<?php

use App\Domain\Fiscal\Relatorio\PMQA\Controller\IndexController;
use App\Domain\Fiscal\Relatorio\PMQA\Controller\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/', [IndexController::class, 'index'])->name('fiscal.relatorio.pmqa.index');
Route::post('{contrato}/store', [StoreController::class, 'index'])->name('fiscal.relatorio.pmqa.store');
