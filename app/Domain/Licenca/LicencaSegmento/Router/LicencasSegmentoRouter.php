<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Licenca\app\Controller\GetLicencaController;
use App\Domain\Licenca\LicencaSegmento\Controller\DeleteLicencaSegmentoController;
use App\Domain\Licenca\LicencaSegmento\Controller\EspacializarLinhaLicencaSegmentoController;
use App\Domain\Licenca\LicencaSegmento\Controller\GetUFLicencaSegmentoController;
use App\Domain\Licenca\LicencaSegmento\Controller\StoreLicencaSegmentoController;
use App\Domain\Licenca\LicencaSegmento\Controller\UpdateLicencaSegmentoController;
use App\Domain\Licenca\LicencaSegmento\Controller\GetLicencaSegmentoController;
use App\Domain\Licenca\LicencaSegmento\Controller\GetTipoPorUfBrLicencaSegmentoController;

// Licença Segmento
Route::get('/get/{licenca}',                 [GetLicencaSegmentoController::class, 'index'])->name('licenca_segmento.get');
Route::post('/store_segmento',               [StoreLicencaSegmentoController::class,  'store'])->name('licenca_segmento.store');
Route::post('/update_segmento/{segmento}',  [UpdateLicencaSegmentoController::class, 'update'])->name('licenca_segmento.update');
Route::delete('/delete_segmento/{segmento}', [DeleteLicencaSegmentoController::class, 'destroy'])->name('licenca_segmento.delete');
Route::get('/get_uf_segmento',               [GetUFLicencaSegmentoController::class,  'index'])->name('licenca_segmento.get_uf');
Route::post('/get_licenca',                  [GetLicencaController::class,            'index'])->name('licenca.get_licenca');
Route::post('/getTipoPorUfBr', [GetTipoPorUfBrLicencaSegmentoController::class, 'getTipoPorUfBr'])->name('licenca_segmento.getTipoPorUfBr_trecho');
Route::post('/espacializarLinha', [EspacializarLinhaLicencaSegmentoController::class, 'espacializarLinha'])->name('licenca_segmento.espacializarLinha_trecho');
