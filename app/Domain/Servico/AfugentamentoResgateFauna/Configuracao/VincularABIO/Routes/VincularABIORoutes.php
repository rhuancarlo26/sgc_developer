<?php

use App\Domain\Servico\AfugentamentoResgateFauna\Configuracao\VincularABIO\Controller\DestroyABIOController;
use App\Domain\Servico\AfugentamentoResgateFauna\Configuracao\VincularABIO\Controller\IndexController;
use App\Domain\Servico\AfugentamentoResgateFauna\Configuracao\VincularABIO\Controller\VincularABIOController;
use Illuminate\Support\Facades\Route;

Route::prefix('/vincularABIO')->group(function () {
    Route::get('{contrato}/{servico}',           [IndexController::class,      'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.configuracao.vincular.abio.index');
    Route::post('{licenca}/{servico}',           [VincularABIOController::class,      'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.configuracao.vincular.abio.create');
    Route::delete('{licenca}',           [DestroyABIOController::class,      'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.configuracao.vincular.abio.delete');
});
