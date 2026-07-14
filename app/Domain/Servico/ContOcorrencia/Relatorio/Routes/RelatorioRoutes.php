<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Servico\ContOcorrencia\Relatorio\Controller\IndexController;
use App\Domain\Servico\ContOcorrencia\Relatorio\Controller\StoreController;
use App\Domain\Servico\ContOcorrencia\Relatorio\Controller\GetVariaveisController;
use App\Domain\Servico\ContOcorrencia\Relatorio\Controller\DeleteController;
use App\Domain\Servico\ContOcorrencia\Relatorio\Controller\UpdateController;
use App\Domain\Servico\ContOcorrencia\Relatorio\Controller\PdfController;


Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.relatorio.index');
Route::post('{contrato}/{servico}/store', [StoreController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.relatorio.store');
Route::post('{contrato}/{servico}/update', [UpdateController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.relatorio.update');
Route::get('{contrato}/{servico}/get_variaveis/{resultado}', [GetVariaveisController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.relatorio.get_variaveis');
Route::delete('{contrato}/{servico}/delete/{relatorio}', [DeleteController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.relatorio.delete');
Route::get('{contrato}/{servico}/gerar_pdf/{relatorio}', [PdfController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.relatorio.gerar_pdf');
