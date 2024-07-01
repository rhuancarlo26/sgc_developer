<?php

use App\Domain\Servico\PMQA\Execucao\Coleta\app\Controller\ShowArquivoController;
use App\Domain\Servico\PMQA\Execucao\Coleta\app\Controller\StoreArquivoController;
use App\Domain\Servico\PMQA\Execucao\Coleta\app\Controller\StoreController;
use App\Domain\Servico\PMQA\Execucao\Coleta\app\Controller\UpdateController;
use Illuminate\Support\Facades\Route;

Route::prefix('/coleta')->group(function () {
  Route::get('{contrato}/{servico}/{campanha}/show_arquivo/{arquivo}',  [ShowArquivoController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.coleta.show_arquivo');
  Route::post('{contrato}/{servico}/{campanha}/store',                  [StoreController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.coleta.store');
  Route::post('{contrato}/{servico}/{campanha}/store_arquivo',          [StoreArquivoController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.coleta.store_arquivo');
  Route::patch('{contrato}/{servico}/{campanha}/update',                [UpdateController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.coleta.update');
});
