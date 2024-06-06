<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Licenca\app\Controller\GetLicencaController;
use App\Domain\Licenca\LicencaSegmento\Controller\DeleteLicencaSegmentoController;
use App\Domain\Licenca\LicencaSegmento\Controller\GetUFLicencaSegmentoController;
use App\Domain\Licenca\LicencaSegmento\Controller\StoreLicencaSegmentoController;
use App\Domain\Licenca\LicencaSegmento\Controller\UpdateLicencaSegmentoController;
use App\Domain\Licenca\LicencaSegmento\Controller\GetLicencaSegmentoController;


// Licença Segmento
Route::get('/get/{licenca}',                 [GetLicencaSegmentoController::class, 'index'])->name('licenca_segmento.get');
Route::post('/store_segmento',               [StoreLicencaSegmentoController::class,  'index'])->name('licenca_segmento.store');
Route::patch('/update_segmento/{segmento}',  [UpdateLicencaSegmentoController::class, 'index'])->name('licenca_segmento.update');
Route::delete('/delete_segmento/{segmento}', [DeleteLicencaSegmentoController::class, 'index'])->name('licenca_segmento.delete');
Route::get('/get_uf_segmento',               [GetUFLicencaSegmentoController::class,  'index'])->name('licenca_segmento.get_uf');
Route::post('/get_licenca',                  [GetLicencaController::class,            'index'])->name('licenca.get_licenca');


