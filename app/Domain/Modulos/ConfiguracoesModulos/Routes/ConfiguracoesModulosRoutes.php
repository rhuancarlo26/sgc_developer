<?php

use App\Domain\Modulos\ConfiguracoesModulos\Controllers\ConfiguracoesModulosController;
use App\Domain\Modulos\ConfiguracoesModulos\Controllers\CreateConfigModuloController;
use App\Domain\Modulos\ConfiguracoesModulos\Controllers\StoreConfigModuloController;
use App\Domain\Modulos\ConfiguracoesModulos\Controllers\UpdateConfigModuloController;
use Illuminate\Support\Facades\Route;

Route::prefix('configuracoes-modulos')->group(function () {

    Route::get('/', [ConfiguracoesModulosController::class, 'index'])->name('config-modulos.index');

    Route::get('/formulario/{modulo?}', [CreateConfigModuloController::class, 'index'])->name('config-modulos.formulario');
    Route::post('/formulario', [StoreConfigModuloController::class, 'store'])->name('config-modulos.store');
    Route::post('/formulario/atualizar/{modulo?}', [UpdateConfigModuloController::class, 'update'])->name('config-modulos.update');
});
