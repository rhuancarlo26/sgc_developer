<?php

use App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Controller\Analise\AnaliseController;
use App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Controller\Analise\CreateAnaliseController;
use App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Controller\Analise\UpdateAnaliseController;
use Illuminate\Support\Facades\Route;

Route::prefix('/Resultado/Analise')->group(function () {
    Route::get('/listagem/{resultado}', [AnaliseController::class, 'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.resultado.analise.get');
    Route::post('create/{resultado}', [CreateAnaliseController::class, 'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.resultado.analise.create');
    Route::patch('update/{analise}', [UpdateAnaliseController::class, 'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.resultado.analise.update');
});