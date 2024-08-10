<?php

use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Controller\CreateController;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Controller\DeleteController;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Controller\DeleteRegistroController;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Controller\EnviarOcorrenciaController;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Controller\IndexController;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Controller\StoreController;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Controller\StoreRegistroController;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Controller\StoreVistoriaController;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Controller\UpdateController;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Controller\VisualizarRegistroController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}/',                                [IndexController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.index');
Route::get('{contrato}/{servico}/create/{ocorrencia?}',            [CreateController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.create');
Route::post('{contrato}/{servico}/store',                          [StoreController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.store');
Route::post('{contrato}/{servico}/store_registro',                 [StoreRegistroController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.store_registro');
Route::post('{contrato}/{servico}/store_vistoria',                 [StoreVistoriaController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.store_vistoria');
Route::get('{contrato}/{servico}/visualizar_registro/{registro}',  [VisualizarRegistroController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.visualizar_registro');
Route::post('{contrato}/{servico}/update',                         [UpdateController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.update');
Route::post('{contrato}/{servico}/enviar_ocorrencia',              [EnviarOcorrenciaController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.enviar_ocorrencia');
Route::get('{contrato}/{servico}/delete/{ocorrencia}',             [DeleteController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.delete');
Route::delete('{contrato}/{servico}/delete_registro/{registro}',   [DeleteRegistroController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.delete_registro');
