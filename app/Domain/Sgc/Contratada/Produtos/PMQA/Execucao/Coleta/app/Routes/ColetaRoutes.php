<?php

use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\Coleta\app\Controller\CreateController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\Coleta\app\Controller\DeleteArquivoController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\Coleta\app\Controller\ShowArquivoController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\Coleta\app\Controller\StoreArquivoController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\Coleta\app\Controller\StoreController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\Coleta\app\Controller\UpdateController;
use Illuminate\Support\Facades\Route;

Route::prefix('/coleta')->group(function () {
    Route::get('{pmqa}/{campanha}/create/{ponto}', [CreateController::class, 'index'])->name('contratos.contratada.sgc.pmqa.execucao.coleta.create');
    Route::get('{pmqa}/{campanha}/show_arquivo/{arquivo}', [ShowArquivoController::class, 'index'])->name('contratos.contratada.sgc.pmqa.execucao.coleta.show_arquivo');
    Route::delete('{pmqa}/{campanha}/delete_arquivo/{arquivo}', [DeleteArquivoController::class, 'index'])->name('contratos.contratada.sgc.pmqa.execucao.coleta.delete_arquivo');
    Route::post('{pmqa}/{campanha}/store/{coleta?}', [StoreController::class, 'index'])->name('contratos.contratada.sgc.pmqa.execucao.coleta.store');
    Route::post('{pmqa}/{campanha}/store_arquivo', [StoreArquivoController::class, 'index'])->name('contratos.contratada.sgc.pmqa.execucao.coleta.store_arquivo');
    Route::patch('{pmqa}/{campanha}/update', [UpdateController::class, 'index'])->name('contratos.contratada.sgc.pmqa.execucao.coleta.update');
});
