<?php

use App\Domain\Fiscal\Relatorio\ContOcorrencia\Controller\IndexController;
use App\Domain\Fiscal\Relatorio\ContOcorrencia\Controller\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/', [IndexController::class, 'index'])->name('fiscal.relatorio.cont_ocorrencia.index');
Route::post('{contrato}/store', [StoreController::class, 'index'])->name('fiscal.relatorio.cont_ocorrencia.store');
