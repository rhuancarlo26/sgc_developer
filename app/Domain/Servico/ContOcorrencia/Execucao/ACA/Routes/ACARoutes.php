<?php

use App\Domain\Servico\ContOcorrencia\Execucao\ACA\Controller\IndexController;
use App\Domain\Servico\ContOcorrencia\Execucao\ACA\Controller\StoreController;
use App\Domain\Servico\ContOcorrencia\Execucao\ACA\Controller\DeleteController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.execucao.aca.index');
Route::post('{contrato}/{servico}/store', [StoreController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.execucao.aca.store');
Route::delete('{contrato}/{servico}/delete/{aca}', [DeleteController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.execucao.controle_rnc.delete');
