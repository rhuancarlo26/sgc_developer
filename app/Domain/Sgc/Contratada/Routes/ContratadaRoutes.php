<?php

use Illuminate\Support\Facades\Route;

use App\Domain\Sgc\Contratada\app\Controller\ContratoSgcController;
use App\Domain\Sgc\Contratada\app\Controller\DadosGeraisSgcController;
use App\Domain\Contrato\Contratada\DadosGerais\Introducao\Controller\StoreIntroducaoContratadaController;


Route::prefix('/contratada')->group(function () {
    Route::get('{contrato}/',                                      [ContratoSgcController::class,                           'index'])->name('sgc.contratada.index');
    Route::get('{contrato}/dados_gerais',                          [DadosGeraisSgcController::class,                         'index'])->name('sgc.contratada.dados_gerais.index');
    Route::post('/store_introducao',                               [StoreIntroducaoContratadaController::class,             'index'])->name('contratos.contratada.store_introducao.index');
    
});
