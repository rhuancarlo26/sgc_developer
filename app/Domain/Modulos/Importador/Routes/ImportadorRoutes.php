<?php

use App\Domain\Modulos\Importador\Controllers\ImportadorController;
use App\Domain\Modulos\Importador\Controllers\CreateImportadorController;
use App\Domain\Modulos\Importador\Controllers\StoreImportadorController;
use Illuminate\Support\Facades\Route;

Route::prefix('importador-modulo')->group(function () {

    Route::get('/', [ImportadorController::class, 'index'])->name('importador.index');

    Route::get('/formulario/{importador?}', [CreateImportadorController::class, 'create'])->name('importador.formulario');
    Route::post('/formulario', [StoreImportadorController::class, 'store'])->name('importador.store');
});
