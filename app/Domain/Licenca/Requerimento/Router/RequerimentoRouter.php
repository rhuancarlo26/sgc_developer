<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Licenca\Requerimento\Controller\DestroyRequerimentoController;
use App\Domain\Licenca\Requerimento\Controller\StoreRequerimentoController;
use App\Domain\Licenca\Requerimento\Controller\visualizarRequerimentoController;

Route::prefix('requerimento')->group(function () {
    Route::post('/store',                    [StoreRequerimentoController::class,      'index'])->name('licenca.requerimento.store');
    Route::get('/visualizar/{requerimento}', [visualizarRequerimentoController::class, 'index'])->name('licenca.requerimento.visualizar');
    Route::delete('/destroy/{requerimento}', [DestroyRequerimentoController::class,    'index'])->name('licenca.requerimento.destroy');
});
