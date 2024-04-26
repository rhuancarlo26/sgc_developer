<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Licenca\Documento\Controller\DeleteLicencaDocumentoController;
use App\Domain\Licenca\Documento\Controller\StoreLicencaDocumentoController;
use App\Domain\Licenca\Documento\Controller\VisualizarDocumentoController;

Route::prefix('documento')->group(function () {
    Route::post('store',                    [StoreLicencaDocumentoController::class, 'index'])->name('licenca.documento.store');
    Route::delete('delete/{documento}',     [DeleteLicencaDocumentoController::class, 'index'])->name('licenca.documento.delete');
    Route::get('/visualizar/{documento}',   [VisualizarDocumentoController::class, 'index'])->name('licenca.documento.visualizar');
});
