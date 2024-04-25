<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Licenca\app\Controller\CreateLicencaController;
use App\Domain\Licenca\app\Controller\GerenciarLicencaController;
use App\Domain\Licenca\app\Controller\GetLicencaController;
use App\Domain\Licenca\app\Controller\IndexController;
use App\Domain\Licenca\app\Controller\SearchLicencaController;
use App\Domain\Licenca\app\Controller\StoreLicencaController;
use App\Domain\Licenca\app\Controller\UpdateLicencaController;
use App\Domain\Licenca\Condicionante\Controller\BuscarLicencaCondicionanteController;
use App\Domain\Licenca\Condicionante\Controller\DestroyCondicionanteController;
use App\Domain\Licenca\Condicionante\Controller\ListagemCondicionanteController;
use App\Domain\Licenca\Condicionante\Controller\StoreCondicionanteController;
use App\Domain\Licenca\Condicionante\Controller\StoreImportacaoCondicionanteController;
use App\Domain\Licenca\Condicionante\Controller\UpdateCondicionanteController;
use App\Domain\Licenca\Documento\Controller\DeleteLicencaDocumentoController;
use App\Domain\Licenca\Documento\Controller\StoreLicencaDocumentoController;
use App\Domain\Licenca\Documento\Controller\VisualizarDocumentoController;
use App\Domain\Licenca\LicencaSegmento\Controller\DeleteLicencaSegmentoController;
use App\Domain\Licenca\LicencaSegmento\Controller\GetUFLicencaSegmentoController;
use App\Domain\Licenca\LicencaSegmento\Controller\StoreLicencaSegmentoController;
use App\Domain\Licenca\LicencaSegmento\Controller\UpdateLicencaSegmentoController;
use App\Domain\Licenca\Requerimento\Controller\DestroyRequerimentoController;
use App\Domain\Licenca\Requerimento\Controller\StoreRequerimentoController;
use App\Domain\Licenca\Requerimento\Controller\visualizarRequerimentoController;
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

    // Licença Segmento
    Route::post('/store_segmento',               [StoreLicencaSegmentoController::class,  'index'])->name('licenca_segmento.store');
    Route::patch('/update_segmento/{segmento}',  [UpdateLicencaSegmentoController::class, 'index'])->name('licenca_segmento.update');
    Route::delete('/delete_segmento/{segmento}', [DeleteLicencaSegmentoController::class, 'index'])->name('licenca_segmento.delete');
    Route::get('/get_uf_segmento',               [GetUFLicencaSegmentoController::class,  'index'])->name('licenca_segmento.get_uf');
    Route::post('/get_licenca',                  [GetLicencaController::class,            'index'])->name('licenca.get_licenca');
    // Trecho
    Route::prefix('trecho')->group(function () {
        Route::post('/get_coordenada_trecho', [GetCoordenadaTrechoController::class, 'index'])->name('licenca.trecho.get_coordenada_trecho');
    });

    // Condicionante
    Route::prefix('condicionante')->group(function () {
        Route::get('/{licenca}',                [ListagemCondicionanteController::class,        'index'])->name('licenca.condicionante.index');
        Route::post('/buscar_licenca',          [BuscarLicencaCondicionanteController::class,   'index'])->name('licenca.condicionante.buscar_licenca');
        Route::post('/store',                   [StoreCondicionanteController::class,           'index'])->name('licenca.condicionante.store');
        Route::post('/store_importacao',        [StoreImportacaoCondicionanteController::class, 'index'])->name('licenca.condicionante.store_importacao');
        Route::patch('/update/{condicionante}', [UpdateCondicionanteController::class,          'index'])->name('licenca.condicionante.update');
        Route::get('/destroy/{condicionante}',  [DestroyCondicionanteController::class,         'index'])->name('licenca.condicionante.destroy');
    });

    // Documento
    Route::prefix('documento')->group(function () {
        Route::post('store',                    [StoreLicencaDocumentoController::class, 'index'])->name('licenca.documento.store');
        Route::delete('delete/{documento}',     [DeleteLicencaDocumentoController::class, 'index'])->name('licenca.documento.delete');
        Route::get('/visualizar/{documento}',   [VisualizarDocumentoController::class, 'index'])->name('licenca.documento.visualizar');
    });

    // Requerimento
    Route::prefix('requerimento')->group(function () {
        Route::post('/store',                    [StoreRequerimentoController::class,      'index'])->name('licenca.requerimento.store');
        Route::get('/visualizar/{requerimento}', [visualizarRequerimentoController::class, 'index'])->name('licenca.requerimento.visualizar');
        Route::delete('/destroy/{requerimento}', [DestroyRequerimentoController::class,    'index'])->name('licenca.requerimento.destroy');
    });
});
