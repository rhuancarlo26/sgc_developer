<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Licenca\Condicionante\Controller\BuscarLicencaCondicionanteController;
use App\Domain\Licenca\Condicionante\Controller\DestroyCondicionanteController;
use App\Domain\Licenca\Condicionante\Controller\ListagemCondicionanteController;
use App\Domain\Licenca\Condicionante\Controller\StoreCondicionanteController;
use App\Domain\Licenca\Condicionante\Controller\StoreImportacaoCondicionanteController;
use App\Domain\Licenca\Condicionante\Controller\UpdateCondicionanteController;

// Condicionante
Route::prefix('condicionante')->group(function () {
    Route::get('/{licenca}',                [ListagemCondicionanteController::class,        'index'])->name('licenca.condicionante.index');
    Route::post('/buscar_licenca',          [BuscarLicencaCondicionanteController::class,   'index'])->name('licenca.condicionante.buscar_licenca');
    Route::post('/store',                   [StoreCondicionanteController::class,           'index'])->name('licenca.condicionante.store');
    Route::post('/store_importacao',        [StoreImportacaoCondicionanteController::class, 'index'])->name('licenca.condicionante.store_importacao');
    Route::patch('/update/{condicionante}', [UpdateCondicionanteController::class,          'index'])->name('licenca.condicionante.update');
    Route::get('/destroy/{condicionante}',  [DestroyCondicionanteController::class,         'index'])->name('licenca.condicionante.destroy');
});
