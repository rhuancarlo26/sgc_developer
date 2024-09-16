<?php

use App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Controller\OutraAnalise\CreateOutraAnaliseController;
use App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Controller\OutraAnalise\DestroyOutraAnaliseController;
use App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Controller\OutraAnalise\OutraAnaliseController;
use App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Controller\OutraAnalise\UpdateOutraAnaliseController;
use Illuminate\Support\Facades\Route;

Route::prefix('/Resultado/OutraAnalise')->group(function () {
    Route::get('/listagem/{resultado}', [OutraAnaliseController::class, 'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.resultado.outra.analise.get');
    Route::post('create/{resultado}', [CreateOutraAnaliseController::class, 'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.resultado.outra.analise.create');
    Route::patch('update/{outraAnalise}', [UpdateOutraAnaliseController::class, 'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.resultado.outra.analise.update');
    Route::delete('destroy/{outraAnalise}', [DestroyOutraAnaliseController::class, 'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.resultado.outra.analise.delete');
});
