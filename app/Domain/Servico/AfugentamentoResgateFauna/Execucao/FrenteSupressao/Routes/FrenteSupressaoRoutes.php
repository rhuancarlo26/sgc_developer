<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Servico\AfugentamentoResgateFauna\Execucao\FrenteSupressao\Controller\FrenteSupressaoController;

Route::prefix('/FrenteSupressao')->group(function () {
    Route::get('{contrato}/{servico}/',           [FrenteSupressaoController::class,      'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.execucao.frente.supressao.index');
    Route::post('/',           [FrenteSupressaoController::class,      'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.execucao.frente.supressao.create');
});