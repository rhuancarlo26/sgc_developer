<?php


use App\Domain\Servico\SupressaoVegetacao\Execucao\Destinacao\Controller\DeleteController;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Destinacao\Controller\DestinacaoArquivoController;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Destinacao\Controller\ExcelExportController;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Destinacao\Controller\IndexController;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Destinacao\Controller\StoreController;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Destinacao\Controller\UpdateController;
use Illuminate\Support\Facades\Route;

Route::prefix('/destinacao')->group(function () {
    Route::get('/{contrato}/{servico}',     IndexController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.destinacao.index');
    Route::get('/excel/{servico}/exportar', ExcelExportController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.destinacao.export');
    Route::post('/store',                   StoreController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.destinacao.store');
    Route::patch('/update',                 UpdateController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.destinacao.update');
    Route::delete('/{destinacao}/delete',   DeleteController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.destinacao.delete');
    Route::post('/arquivo/upload', [DestinacaoArquivoController::class, 'store'])->name('contratos.contratada.servicos.supressao-vegetacao.execucao.destinacao.arquivo.upload');
    Route::get('/arquivo/{destinacao}/listar', [DestinacaoArquivoController::class, 'listar'])->name('contratos.contratada.servicos.supressao-vegetacao.execucao.destinacao.arquivo.listar');
    Route::delete('/arquivo/{destinacao}/{arquivo}', [DestinacaoArquivoController::class, 'destroy'])->name('contratos.contratada.servicos.supressao-vegetacao.execucao.destinacao.arquivo.delete');
});
