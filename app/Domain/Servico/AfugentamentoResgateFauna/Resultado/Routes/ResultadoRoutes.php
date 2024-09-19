<?php

use App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Controller\CreateResultadoController;
use App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Controller\DestroyResultadoController;
use App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Controller\GetResultadoController;
use App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Controller\ResultadoController;
use App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Controller\UpdateResultadoController;
use Illuminate\Support\Facades\Route;

Route::prefix('/Resultado')->group(function () {
    Route::get('/listagem/{contrato}/{servico}', [ResultadoController::class, 'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.resultado.index');
    Route::get('/dados/{contrato}/{servico}', [ResultadoController::class, 'getResultados'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.resultado.dados');
    Route::post('create/{servico}', [CreateResultadoController::class, 'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.resultado.create');
    Route::patch('update/{resultado}', [UpdateResultadoController::class, 'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.resultado.update');
    Route::delete('delete/{resultado}', [DestroyResultadoController::class, 'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.resultado.delete');
    Route::get('get/{resultado}', [GetResultadoController::class, 'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.resultado.get');
});