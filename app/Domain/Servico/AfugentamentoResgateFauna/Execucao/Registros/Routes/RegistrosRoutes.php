<?php

use App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Controller\RegistrosController;
use Illuminate\Support\Facades\Route;


Route::prefix('/Registros')->group(function () {
    Route::get('{contrato}/{servico}/',           [RegistrosController::class,      'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.execucao.registros.index');
});