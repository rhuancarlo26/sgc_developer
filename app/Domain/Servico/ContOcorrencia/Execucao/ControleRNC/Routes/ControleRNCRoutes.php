<?php

use App\Domain\Servico\ContOcorrencia\Execucao\ControleRNC\Controller\IndexController;
use App\Domain\Servico\ContOcorrencia\Execucao\ControleRNC\Controller\UpdateController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.execucao.controle_rnc.index');
Route::post('{contrato}/{servico}/update', [UpdateController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.execucao.controle_rnc.update');
