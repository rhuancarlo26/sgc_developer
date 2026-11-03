<?php

use App\Domain\Sgc\Licencas\Controller\CondicionanteController;
use App\Domain\Sgc\Licencas\Controller\LicencaController;
use App\Domain\Sgc\Licencas\Controller\PainelController;
use App\Domain\Sgc\Licencas\Controller\PrazoController;
use Illuminate\Support\Facades\Route;

// Gestão de prazos de licenças e autorizações ambientais. Cada rota nomeada vira uma permissão em Perfis.
Route::prefix('licencas')->name('sgc.licencas.')->group(function () {
    Route::get('/painel',                                [PainelController::class,        'index'])->name('painel');
    Route::get('/regularizacao',                         [PainelController::class,        'regularizacao'])->name('regularizacao');

    Route::get('/',                                      [LicencaController::class,       'index'])->name('index');
    Route::get('/create',                                [LicencaController::class,       'create'])->name('create');
    Route::post('/',                                     [LicencaController::class,       'store'])->name('store');
    Route::get('/{licenca}',                             [LicencaController::class,       'show'])->name('show');
    Route::get('/{licenca}/edit',                        [LicencaController::class,       'edit'])->name('edit');
    Route::post('/{licenca}',                            [LicencaController::class,       'update'])->name('update');
    Route::delete('/{licenca}',                          [LicencaController::class,       'destroy'])->name('destroy');
    Route::patch('/{licenca}/arquivar',                  [LicencaController::class,       'arquivar'])->name('arquivar');
    Route::get('/{licenca}/arquivo',                     [LicencaController::class,       'arquivo'])->name('arquivo');

    Route::post('/{licenca}/condicionantes',             [CondicionanteController::class, 'store'])->name('condicionantes.store');
    Route::put('/condicionantes/{condicionante}',        [CondicionanteController::class, 'update'])->name('condicionantes.update');
    Route::delete('/condicionantes/{condicionante}',     [CondicionanteController::class, 'destroy'])->name('condicionantes.destroy');

    Route::post('/{licenca}/eventos',                    [PrazoController::class,         'evento'])->name('prazos.evento');
    Route::post('/prazos/{prazo}/registros',             [PrazoController::class,         'registrar'])->name('prazos.registros.store');
    Route::delete('/registros/{registro}',               [PrazoController::class,         'removerRegistro'])->name('prazos.registros.destroy');
    Route::get('/registros/{registro}/arquivo',          [PrazoController::class,         'arquivoRegistro'])->name('prazos.registros.arquivo');
});
