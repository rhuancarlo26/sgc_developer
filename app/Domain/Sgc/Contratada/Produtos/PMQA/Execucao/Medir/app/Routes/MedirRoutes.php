<?php

use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\Medir\app\Controller\CreateController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\Medir\app\Controller\DeleteArquivoController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\Medir\app\Controller\ShowArquivoController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\Medir\app\Controller\StoreArquivoController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\Medir\app\Controller\StoreController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\Medir\app\Controller\UpdateController;
use Illuminate\Support\Facades\Route;

Route::prefix('/medir')->group(function () {
  Route::get('{pmqa}/{campanha}/create/{ponto}',              [CreateController::class, 'index'])->name('contratos.contratada.sgc.pmqa.execucao.medir.create');
  Route::post('{pmqa}/{campanha}/store/{ponto}',              [StoreController::class, 'index'])->name('contratos.contratada.sgc.pmqa.execucao.medir.store');
  Route::post('{pmqa}/{campanha}/store_arquivo/{ponto}',      [StoreArquivoController::class, 'index'])->name('contratos.contratada.sgc.pmqa.execucao.medir.store_arquivo');
  Route::get('{pmqa}/{campanha}/show_arquivo/{arquivo}',      [ShowArquivoController::class, 'index'])->name('contratos.contratada.sgc.pmqa.execucao.medir.show_arquivo');
  Route::delete('{pmqa}/{campanha}/delete_arquivo/{arquivo}', [DeleteArquivoController::class, 'index'])->name('contratos.contratada.sgc.pmqa.execucao.medir.delete_arquivo');
  Route::patch('{servico}/{campanha}/update/{ponto}',            [UpdateController::class, 'index'])->name('contratos.contratada.sgc.pmqa.execucao.medir.update');
});
