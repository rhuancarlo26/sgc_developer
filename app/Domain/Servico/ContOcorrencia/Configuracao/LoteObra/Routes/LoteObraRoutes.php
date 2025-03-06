<?php

use App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Controller\CreateController;
use App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Controller\DeleteController;
use App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Controller\EnviarLoteFiscalController;
use App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Controller\IndexController;
use App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Controller\StoreController;
use App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Controller\UpdateController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.configuracao.lote_obra.index');
Route::get('{contrato}/{servico}/create/{lote?}', [CreateController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.configuracao.lote_obra.create');
Route::post('{contrato}/{servico}/store', [StoreController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.configuracao.lote_obra.store');
Route::post('{contrato}/{servico}/update', [UpdateController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.configuracao.lote_obra.update');
Route::delete('{contrato}/{servico}/delete/{lote}', [DeleteController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.configuracao.lote_obra.delete');
Route::post('{contrato}/{servico}/enviar_lote_fiscal', [EnviarLoteFiscalController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.configuracao.lote_obra.enviar_lote_fiscal');
