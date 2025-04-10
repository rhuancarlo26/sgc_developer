<?php

use App\Domain\Licenca\Shapefile\Controller\DeleteLicencaShapefileController;
use App\Domain\Licenca\Shapefile\Controller\StoreLicencaShapefileController;
use Illuminate\Support\Facades\Route;

Route::prefix('shapefile')->group(function () {
  Route::post('store',                [StoreLicencaShapefileController::class, 'index'])->name('licenca.shapefile.store');
  Route::delete('delete/{shapefile}', [DeleteLicencaShapefileController::class, 'index'])->name('licenca.shapefile.delete');
});
