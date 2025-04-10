<?php

use App\Domain\Servico\AfugentamentoResgateFauna\Relatorio\Controller\CreateAnexoRelatorioController;
use App\Domain\Servico\AfugentamentoResgateFauna\Relatorio\Controller\CreateConclusaoRelatorioController;
use App\Domain\Servico\AfugentamentoResgateFauna\Relatorio\Controller\CreateRelatorioController;
use App\Domain\Servico\AfugentamentoResgateFauna\Relatorio\Controller\DestroyAnexoRelatorioController;
use App\Domain\Servico\AfugentamentoResgateFauna\Relatorio\Controller\DestroyRelatorioController;
use App\Domain\Servico\AfugentamentoResgateFauna\Relatorio\Controller\GetRelatorioController;
use App\Domain\Servico\AfugentamentoResgateFauna\Relatorio\Controller\RelatorioController;
use App\Domain\Servico\AfugentamentoResgateFauna\Relatorio\Controller\UpdateConclusaoRelatorioController;
use App\Domain\Servico\AfugentamentoResgateFauna\Relatorio\Controller\UpdateRelatorioController;
use Illuminate\Support\Facades\Route;

Route::prefix('/Relatorio')->group(function () {
    Route::get('/listagem/{contrato}/{servico}', [RelatorioController::class, 'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.relatorios.index');
    Route::post('/create/{servico}', [CreateRelatorioController::class, 'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.relatorios.create');
    Route::patch('/update/{relatorio}', [UpdateRelatorioController::class, 'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.relatorios.update');
    Route::post('/create-anexo/{relatorio}', [CreateAnexoRelatorioController::class, 'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.relatorios.anexo.create');
    Route::post('/create-conclusao/{relatorio}', [CreateConclusaoRelatorioController::class, 'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.relatorios.create.conclusao');
    Route::patch('/update-conclusao/{relatorio}', [UpdateConclusaoRelatorioController::class, 'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.relatorios.update.conclusao');
    Route::get('/get-anexo/{relatorio}', [GetRelatorioController::class, 'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.relatorios.anexo.get');
    Route::delete('destroy/{relatorio}', [DestroyRelatorioController::class, 'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.relatorios.delete');
    Route::delete('destroy-anexo/{anexo}', [DestroyAnexoRelatorioController::class, 'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.relatorios.anexo.delete');
});