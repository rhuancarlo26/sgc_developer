<?php

use App\Domain\Modulos\Importador\Controllers\ImportadorController;
use App\Domain\Modulos\Importador\Controllers\CreateImportadorController;
use App\Domain\Modulos\Importador\Controllers\DadosImportadorController;
use App\Domain\Modulos\Importador\Controllers\DestroyImportadorController;
use App\Domain\Modulos\Importador\Controllers\StoreImportadorController;
use App\Domain\Modulos\Importador\Controllers\UpdateImportadorController;
use App\Domain\Modulos\Importador\Controllers\StatusImportadorController;
use App\Domain\Modulos\Importador\Controllers\HistoricoImportadorController;
use Illuminate\Support\Facades\Route;

Route::prefix('importador-modulo')->group(function () {

    Route::get('/', [ImportadorController::class, 'index'])->name('importador.index');

    Route::get('/formulario/{importador?}', [CreateImportadorController::class, 'create'])->name('importador.formulario');
    Route::post('/formulario', [StoreImportadorController::class, 'store'])->name('importador.store');
    Route::post('/atualizar-formulario/{importador}', [UpdateImportadorController::class, 'update'])->name('importador.update');
    Route::delete('/excluir/{importador}', [DestroyImportadorController::class, 'destroy'])->name('importador.destroy');

    Route::post('/enviar-analise/{importador}', [StatusImportadorController::class, 'enviarAnalise'])->name('importador.enviarAnalise');
    Route::post('/aprov-reprov/{importador}/{status}', [StatusImportadorController::class, 'aprovReprov'])->name('importador.aprovReprov');

    Route::get('/buscar-dados/{importador}', [DadosImportadorController::class, 'buscarDados'])->name('importador.buscarDados');
    Route::post('/importar-planilha/{importador}', [DadosImportadorController::class, 'importarPlanilha'])->name('importador.importarPlanilha');
    Route::delete('/excluir-dados/{importador}', [DadosImportadorController::class, 'excluirDados'])->name('importador.excluirDados');

    Route::get('/buscar-historico/{importador}', [HistoricoImportadorController::class, 'buscarHistorico'])->name('importador.buscarHistorico');
});
