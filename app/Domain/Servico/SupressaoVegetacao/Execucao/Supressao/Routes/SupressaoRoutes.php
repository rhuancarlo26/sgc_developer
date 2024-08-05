<?php

use App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Controller\DeleteController;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Controller\DeleteCorteEspecieController;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Controller\DeleteFotoController;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Controller\ExcelExportController;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Controller\IndexController;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Controller\StoreController;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Controller\UpdateController;
use Illuminate\Support\Facades\Route;

Route::prefix('/supressao')->group(function () {
    Route::get('/{contrato}/{servico}',             IndexController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.supressao.index');
    Route::get('/excel/{servico}/exportar',         ExcelExportController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.supressao.export');
    Route::post('/store',                           StoreController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.supressao.store');
    Route::patch('/update',                         UpdateController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.supressao.update');
    Route::delete('{area}/delete',                  DeleteController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.supressao.delete');
    Route::delete('fotos/{arquivo}/{area}/deletar', DeleteFotoController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.supressao.fotos.delete');
    Route::delete('corte-especies/{corte}/delete',  DeleteCorteEspecieController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.supressao.corete-especies.delete');
});
