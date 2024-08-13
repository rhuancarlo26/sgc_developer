<?php


use App\Domain\Servico\SupressaoVegetacao\Execucao\Destinacao\Controller\DeleteController;
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
});
