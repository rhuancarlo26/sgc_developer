<?php


use App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Controller\GetCorteEspecieJsonController;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Controller\IndexController;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Controller\StoreController;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Controller\UpdateController;
use Illuminate\Support\Facades\Route;

Route::prefix('/pilhas')->group(function () {
    Route::get('/{contrato}/{servico}',             IndexController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.pilhas.index');
    Route::get('/{supressao}',                      GetCorteEspecieJsonController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.pilhas.get-corte-especie');
//    Route::get('/excel/{servico}/exportar',         ExcelExportController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.supressao.export');
    Route::post('/store',                           StoreController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.pilhas.store');
    Route::patch('/update',                         UpdateController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.pilhas.update');
//    Route::delete('{area}/delete',                  DeleteController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.supressao.delete');
//    Route::delete('fotos/{arquivo}/{area}/deletar', DeleteFotoController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.supressao.fotos.delete');
//    Route::delete('corte-especies/{corte}/delete',  DeleteCorteEspecieController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.supressao.corete-especies.delete');
});
