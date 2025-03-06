<?php

use App\Domain\Servico\AfugentamentoResgateFauna\Configuracao\VincularASV\Controller\DestroyASVController;
use App\Domain\Servico\AfugentamentoResgateFauna\Configuracao\VincularASV\Controller\IndexController;
use App\Domain\Servico\AfugentamentoResgateFauna\Configuracao\VincularASV\Controller\VincularASVController;
use App\Domain\Servico\PMQA\Configuracao\Parametro\Controller\DestroyController;
use Illuminate\Support\Facades\Route;

Route::prefix('/vincularASV')->group(function () {
    Route::get('{contrato}/{servico}/',    [IndexController::class,            'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.configuracao.vincular.asv.index');
    Route::post('{licenca}/{servico}',     [VincularASVController::class,      'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.configuracao.vincular.asv.create');
    Route::delete('{licenca}',             [DestroyASVController::class,       'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.configuracao.vincular.asv.delete');
});
