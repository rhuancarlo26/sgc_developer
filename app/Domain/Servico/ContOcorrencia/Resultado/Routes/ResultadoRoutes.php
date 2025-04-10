<?php

use App\Domain\Servico\ContOcorrencia\Resultado\Controller\IndexController;
use App\Domain\Servico\ContOcorrencia\Resultado\Controller\ResultadoController;
use App\Domain\Servico\ContOcorrencia\Resultado\Controller\StoreController;
use App\Domain\Servico\ContOcorrencia\Resultado\Controller\StoreOutraAnaliseController;
use App\Domain\Servico\ContOcorrencia\Resultado\Controller\StoreAnaliseController;
use App\Domain\Servico\ContOcorrencia\Resultado\Controller\UpdateOutraAnaliseController;
use App\Domain\Servico\ContOcorrencia\Resultado\Controller\VisualizarOutraAnalise;
use App\Domain\Servico\ContOcorrencia\Resultado\Controller\UpdateController;
use App\Domain\Servico\ContOcorrencia\Resultado\Controller\DeleteController;
use App\Domain\Servico\ContOcorrencia\Resultado\Controller\DeleteOutraAnaliseController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.resultado.index');
Route::get('{contrato}/{servico}/resultado/{resultado}', [ResultadoController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.resultado.resultado');
Route::get('{contrato}/{servico}/{resultado}/visualizar/{outraAnalise}', [VisualizarOutraAnalise::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.resultado.visualizar');
Route::post('{contrato}/{servico}/store', [StoreController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.resultado.store');
Route::post('{contrato}/{servico}/{resultado}/store_outra_analise', [StoreOutraAnaliseController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.resultado.store_outra_analise');
Route::post('{contrato}/{servico}/{resultado}/store_analise', [StoreAnaliseController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.resultado.store_analise');
Route::post('{contrato}/{servico}/update', [UpdateController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.resultado.update');
Route::post('{contrato}/{servico}/{resultado}/update_outra_analise', [UpdateOutraAnaliseController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.resultado.update_outra_analise');
Route::delete('{contrato}/{servico}/delete/{resultado}', [DeleteController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.resultado.delete');
Route::delete('{contrato}/{servico}/{resultado}/delete_outra_analise/{outraAnalise}', [DeleteOutraAnaliseController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.resultado.delete_outra_analise');
