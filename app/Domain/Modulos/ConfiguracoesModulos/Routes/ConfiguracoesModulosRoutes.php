<?php

use App\Domain\Modulos\ConfiguracoesModulos\Controllers\ConfiguracoesModulosController;
use App\Domain\Modulos\ConfiguracoesModulos\Controllers\CreateConfigModuloController;
use Illuminate\Support\Facades\Route;

Route::prefix('configuracoes-modulos')->group(function () {

    Route::get('/', [ConfiguracoesModulosController::class, 'index'])->name('config-modulos.index');

    Route::get('/formulario/{modulo?}', [CreateConfigModuloController::class, 'index'])->name('config-modulos.formulario');
});
