<?php

use App\Domain\Servico\AfugentamentoResgateFauna\Execucao\FrenteSupressao\Controller\CreateFrenteSupressaoController;
use App\Domain\Servico\AfugentamentoResgateFauna\Execucao\FrenteSupressao\Controller\DestroyFrenteSupressaoController;
use App\Domain\Servico\AfugentamentoResgateFauna\Execucao\FrenteSupressao\Controller\FrenteSupressaoController;
use App\Domain\Servico\AfugentamentoResgateFauna\Execucao\FrenteSupressao\Controller\UpdateFrenteSupressaoController;
use Illuminate\Support\Facades\Route;

Route::prefix('/FrenteSupressao')->group(function () {
    Route::get('{contrato}/{servico}/',     [FrenteSupressaoController::class,            'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.execucao.frente.supressao.index');
    Route::post('/{servico}',               [CreateFrenteSupressaoController::class,      'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.execucao.frente.supressao.create');
    Route::delete('/{frente}',              [DestroyFrenteSupressaoController::class,     'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.execucao.frente.supressao.delete');
    Route::patch('/{frente}',               [UpdateFrenteSupressaoController::class,      'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.execucao.frente.supressao.update');
});