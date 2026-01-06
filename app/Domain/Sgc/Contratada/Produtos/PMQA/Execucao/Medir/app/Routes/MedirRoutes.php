<?php

use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\Medir\app\Controller\CreateController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\Medir\app\Controller\DeleteArquivoController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\Medir\app\Controller\ShowArquivoController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\Medir\app\Controller\StoreArquivoController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\Medir\app\Controller\StoreController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\Medir\app\Controller\UpdateController;
use Illuminate\Support\Facades\Route;

Route::prefix('/medir')->group(function () {
  Route::get('{servico}/{campanha}/create/{ponto}',              [CreateController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.medir.create');
  Route::post('{servico}/{campanha}/store/{ponto}',              [StoreController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.medir.store');
  Route::post('{servico}/{campanha}/store_arquivo/{ponto}',      [StoreArquivoController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.medir.store_arquivo');
  Route::get('{servico}/{campanha}/show_arquivo/{arquivo}',      [ShowArquivoController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.medir.show_arquivo');
  Route::delete('{servico}/{campanha}/delete_arquivo/{arquivo}', [DeleteArquivoController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.medir.delete_arquivo');
  Route::patch('{servico}/{campanha}/update/{ponto}',            [UpdateController::class, 'index'])->name('contratos.contratada.servicos.pmqa.execucao.medir.update');
});
