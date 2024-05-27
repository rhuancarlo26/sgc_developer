<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Licenca\app\Controller\CreateLicencaController;
use App\Domain\Licenca\app\Controller\GerenciarLicencaController;
use App\Domain\Licenca\app\Controller\IndexController;
use App\Domain\Licenca\app\Controller\SearchLicencaController;
use App\Domain\Licenca\app\Controller\StoreLicencaController;
use App\Domain\Licenca\app\Controller\UpdateLicencaController;
use App\Domain\Licenca\Trecho\Controller\GetCoordenadaTrechoController;

Route::prefix('licenca')->group(function () {
    // Licença
    Route::get('/',                   [IndexController::class,         'index'])->name('licenca.index');
    Route::get('/arquivo/{arquivo}',  [IndexController::class,         'index'])->name('licenca.arquivo');
    Route::get('/create/{licenca?}',  [CreateLicencaController::class, 'index'])->name('licenca.create');
    Route::post('/store',             [StoreLicencaController::class,  'index'])->name('licenca.store');
    Route::patch('/update/{licenca}', [UpdateLicencaController::class, 'index'])->name('licenca.update');
    Route::get('/search/{licenca?}',  [SearchLicencaController::class, 'index'])->name('licenca.search')->where('licenca', '(.*)');
    Route::patch('/gerenciar-licenca/{licenca}', [GerenciarLicencaController::class, 'index'])->name('licenca.gerenciar-licenca');

    // Trecho
    Route::prefix('trecho')->group(function () {
        Route::post('/get_coordenada_trecho', [GetCoordenadaTrechoController::class, 'index'])->name('licenca.trecho.get_coordenada_trecho');
    });

    // Licença Segmento
    require __DIR__ . '/../../LicencaSegmento/Router/LicencasSegmentoRouter.php';

    // Condicionante
    require __DIR__ . '/../../Condicionante/Router/CondicionantesRouter.php';

    // Documento
    require __DIR__ . '/../../Documento/Router/DocumentosRouter.php';

    // Documento
    require __DIR__ . '/../../Shapefile/Router/ShapefileRouter.php';

    // Requerimento
    require __DIR__ . '/../../Requerimento/Router/RequerimentoRouter.php';
});
