<?php

use App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Controller\CreateController;
use App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Controller\IndexController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.configuracao.lote_obra.index');
Route::get('{contrato}/{servico}/create/{lote?}', [CreateController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.configuracao.lote_obra.create');