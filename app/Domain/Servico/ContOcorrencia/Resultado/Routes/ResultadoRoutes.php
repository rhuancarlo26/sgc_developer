<?php

use App\Domain\Servico\ContOcorrencia\Resultado\Controller\IndexController;
use App\Domain\Servico\ContOcorrencia\Resultado\Controller\StoreController;
use App\Domain\Servico\ContOcorrencia\Resultado\Controller\UpdateController;
use App\Domain\Servico\ContOcorrencia\Resultado\Controller\DeleteController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.resultado.index');
Route::post('{contrato}/{servico}/store', [StoreController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.resultado.store');
Route::post('{contrato}/{servico}/update', [UpdateController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.resultado.update');
Route::delete('{contrato}/{servico}/delete/{resultado}', [DeleteController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.resultado.delete');
