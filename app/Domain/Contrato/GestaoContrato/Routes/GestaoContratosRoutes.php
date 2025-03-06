<?php

use App\Domain\Contrato\GestaoContrato\Aditivo\Controller\DestroyAditivoController;
use App\Domain\Contrato\GestaoContrato\Aditivo\Controller\StoreAditivoController;
use App\Domain\Contrato\GestaoContrato\Aditivo\Controller\UpdateAditivoController;
use Illuminate\Support\Facades\Route;

use App\Domain\Contrato\GestaoContrato\Controller\CreateContratoController;
use App\Domain\Contrato\GestaoContrato\Controller\DestroyContratoController;
use App\Domain\Contrato\GestaoContrato\Controller\DestroyContratoTrechoController;
use App\Domain\Contrato\GestaoContrato\Controller\ExcelExportContratoController;
use App\Domain\Contrato\GestaoContrato\Controller\GetCoordenadaController;
use App\Domain\Contrato\GestaoContrato\Controller\GetGeoJsonController;
use App\Domain\Contrato\GestaoContrato\Controller\ListagemContratoController;
use App\Domain\Contrato\GestaoContrato\Controller\StoreContratoController;
use App\Domain\Contrato\GestaoContrato\Controller\StoreContratoTrechoController;
use App\Domain\Contrato\GestaoContrato\Controller\UpdateContratoController;
use App\Domain\Contrato\GestaoContrato\Controller\UpdateContratoTrechoController;

Route::get('excel', [ExcelExportContratoController::class, 'excelExport'])->name('contratos.gestao.excel_export');

Route::prefix('gestao')->group(function () {
    Route::get('/{tipo}', [ListagemContratoController::class, 'index'])->name('contratos.gestao.listagem');
    Route::get('/create/{tipo?}/{contrato?}', [CreateContratoController::class, 'create'])->name('contratos.gestao.create');
    Route::post('/store', [StoreContratoController::class, 'store'])->name('contratos.gestao.store');
    Route::post('/atualizar', [UpdateContratoController::class, 'update'])->name('contratos.gestao.atualizar');
    Route::get('/delete/{contrato}', [DestroyContratoController::class, 'destroy'])->name('contratos.gestao.delete');
//    Route::get('excel', [ExcelExportContratoController::class, 'index'])->name('contratos.gestao.excel');
    Route::post('/get_coordenada', [GetCoordenadaController::class, 'getCoordenada'])->name('contratos.gestao.get_coordenada');
    Route::post('/get_geo_json', [GetGeoJsonController::class, 'getGeoJson'])->name('contratos.gestao.get_geo_json');
    Route::post('/store_trecho', [StoreContratoTrechoController::class, 'storeTrecho'])->name('contratos.gestao.store_trecho');
    Route::post('/atualizar_trecho', [UpdateContratoTrechoController::class, 'updateTrecho'])->name('contratos.gestao.update_trecho');
    Route::delete('/delete_trecho/{tipo}/{trecho}', [DestroyContratoTrechoController::class, 'destroyTrecho'])->name('contratos.gestao.delete_trecho');

    Route::prefix('aditivo')->group(function () {
        Route::post('/store', [StoreAditivoController::class, 'index'])->name('contratos.gestao.aditivo.store');
        Route::patch('/update', [UpdateAditivoController::class, 'index'])->name('contratos.gestao.aditivo.update');
        Route::delete('/destroy/{tipo}/{aditivo}', [DestroyAditivoController::class, 'index'])->name('contratos.gestao.aditivo.destroy');
    });
});
