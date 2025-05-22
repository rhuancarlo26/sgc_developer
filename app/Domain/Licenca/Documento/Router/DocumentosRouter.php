<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Licenca\Documento\Controller\DeleteLicencaDocumentoController;
use App\Domain\Licenca\Documento\Controller\StoreLicencaDocumentoController;
use App\Domain\Licenca\Documento\Controller\VisualizarDocumentoController;

Route::prefix('documento')->group(function () {
    Route::post('store',                    [StoreLicencaDocumentoController::class, 'index'])->name('licenca.documento.store');
    Route::delete('delete/{licenca}',       [DeleteLicencaDocumentoController::class, 'index'])->name('licenca.documento.delete');
    Route::get('/visualizar/{licenca}',     [VisualizarDocumentoController::class, 'index'])->name('licenca.documento.visualizar');


    Route::post('storeTermo',                    [StoreLicencaDocumentoController::class, 'termo'])->name('licenca.documento.termo.store');
    Route::delete('deleteTermo/{licenca}',       [DeleteLicencaDocumentoController::class, 'termo'])->name('licenca.documento.termo.delete');
    Route::get('/visualizarTermo/{licenca}',     [VisualizarDocumentoController::class, 'termo'])->name('licenca.documento.termo.visualizar');
});
