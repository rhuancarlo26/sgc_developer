<?php

use App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Controller\CreateRegistroController;
use App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Controller\DestroyRegistroController;
use App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Controller\DownloadRegistroController;
use App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Controller\RegistrosController;
use App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Controller\UpdateRegistroController;
use Illuminate\Support\Facades\Route;


Route::prefix('/Registros')->group(function () {
    Route::get('{contrato}/{servico}/',     [RegistrosController::class,        'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.execucao.registros.index');
    Route::post('/{servico}',               [CreateRegistroController::class,   'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.execucao.registro.create');
    Route::patch('/{registro}',             [UpdateRegistroController::class,   'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.execucao.registro.update');
    Route::delete('/{registro}',            [DestroyRegistroController::class,  'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.execucao.registros.delete');
    Route::get('{servico}/download',        [DownloadRegistroController::class, 'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.execucao.registros.download');
});