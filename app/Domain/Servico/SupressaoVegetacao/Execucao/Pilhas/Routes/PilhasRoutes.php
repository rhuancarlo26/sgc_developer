<?php


use App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Controller\DeleteController;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Controller\DeleteFotoController;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Controller\ExcelExportController;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Controller\GetCorteEspecieJsonController;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Controller\IndexController;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Controller\StoreController;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Controller\UpdateController;
use Illuminate\Support\Facades\Route;

Route::prefix('/pilhas')->group(function () {
    Route::get('/{contrato}/{servico}',               IndexController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.pilhas.index');
    Route::get('/{supressao}',                        GetCorteEspecieJsonController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.pilhas.get-corte-especie');
    Route::get('/excel/{servico}/exportar',           ExcelExportController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.pilhas.export');
    Route::post('/store',                             StoreController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.pilhas.store');
    Route::patch('/update',                           UpdateController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.pilhas.update');
    Route::delete('/{pilha}/delete',                  DeleteController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.pilhas.delete');
    Route::delete('/fotos/{arquivo}/{pilha}/deletar', DeleteFotoController::class)->name('contratos.contratada.servicos.supressao-vegetacao.execucao.pilhas.fotos.delete');
});
