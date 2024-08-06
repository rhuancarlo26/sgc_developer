<?php

use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Controller\CreateController;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Controller\IndexController;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Controller\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.index');
Route::get('{contrato}/{servico}/create/{ocorrencia?}', [CreateController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.create');
Route::post('{contrato}/{servico}/store', [StoreController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.store');
