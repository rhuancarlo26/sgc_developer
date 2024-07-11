<?php

use App\Domain\Servico\PMQA\Relatorio\app\Controller\IndexController;
use App\Domain\Servico\PMQA\Relatorio\app\Controller\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}', [IndexController::class, 'index'])->name('contratos.contratada.servicos.pmqa.relatorio.index');
Route::post('{contrato}/{servico}/store', [StoreController::class, 'index'])->name('contratos.contratada.servicos.pmqa.relatorio.store');