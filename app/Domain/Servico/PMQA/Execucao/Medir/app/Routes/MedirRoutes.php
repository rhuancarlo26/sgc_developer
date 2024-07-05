<?php

use App\Domain\Servico\PMQA\Execucao\Medir\app\Controller\CreateController;
use App\Domain\Servico\PMQA\Execucao\Medir\app\Controller\DeleteArquivoController;
use App\Domain\Servico\PMQA\Execucao\Medir\app\Controller\ShowArquivoController;
use App\Domain\Servico\PMQA\Execucao\Medir\app\Controller\StoreArquivoController;
use App\Domain\Servico\PMQA\Execucao\Medir\app\Controller\StoreController;
use App\Domain\Servico\PMQA\Execucao\Medir\app\Controller\UpdateController;
use Illuminate\Support\Facades\Route;

Route::prefix('/medir')->group(function () {
  Route::get('{contrato}/{servico}/{campanha}/create/{ponto}',              [CreateController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.medir.create');
  Route::post('{contrato}/{servico}/{campanha}/store/{ponto}',              [StoreController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.medir.store');
  Route::post('{contrato}/{servico}/{campanha}/store_arquivo/{ponto}',      [StoreArquivoController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.medir.store_arquivo');
  Route::get('{contrato}/{servico}/{campanha}/show_arquivo/{arquivo}',      [ShowArquivoController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.medir.show_arquivo');
  Route::delete('{contrato}/{servico}/{campanha}/delete_arquivo/{arquivo}', [DeleteArquivoController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.medir.delete_arquivo');
  Route::patch('{contrato}/{servico}/{campanha}/update/{ponto}',            [UpdateController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.medir.update');
});