<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Contrato\Contratada\Recurso\Veiculo\Controller\CreateVeiculoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Veiculo\Controller\DestroyDocumentoVeiculoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Veiculo\Controller\DestroyQuilometragemVeiculoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Veiculo\Controller\DestroyVeiculoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Veiculo\Controller\ListagemVeiculoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Veiculo\Controller\StoreDocumentoVeiculoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Veiculo\Controller\StoreQuilometragemVeiculoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Veiculo\Controller\StoreVeiculoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Veiculo\Controller\UpdateQuilometragemVeiculoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Veiculo\Controller\UpdateVeiculoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Veiculo\Controller\VisualizarDocumentoVeiculoRecursoController;

//Veiculo
Route::get('{contrato}/recurso/veiculo',                                [ListagemVeiculoRecursoController::class,               'index'])->name('contratos.contratada.recurso.veiculo.index');
Route::get('{contrato}/recurso/veiculo/create/{veiculo?}',              [CreateVeiculoRecursoController::class,                 'index'])->name('contratos.contratada.recurso.veiculo.create');
Route::post('recurso/veiculo/store',                                    [StoreVeiculoRecursoController::class,                  'index'])->name('contratos.contratada.recurso.veiculo.store');
Route::patch('recurso/veiculo/update',                                  [UpdateVeiculoRecursoController::class,                 'index'])->name('contratos.contratada.recurso.veiculo.update');
Route::post('recurso/veiculo/store_documento',                          [StoreDocumentoVeiculoRecursoController::class,         'index'])->name('contratos.contratada.recurso.veiculo.store_documento');
Route::post('recurso/veiculo/store_quilometragem',                      [StoreQuilometragemVeiculoRecursoController::class,     'index'])->name('contratos.contratada.recurso.veiculo.store_quilometragem');
Route::patch('recurso/veiculo/update_quilometragem',                    [UpdateQuilometragemVeiculoRecursoController::class,    'index'])->name('contratos.contratada.recurso.veiculo.update_quilometragem');
Route::get('recurso/veiculo/visualizar/{documento}',                    [VisualizarDocumentoVeiculoRecursoController::class,    'index'])->name('contratos.contratada.recurso.veiculo.visualizar');
Route::delete('{contrato}/recurso/veiculo/destroy/{documento}',         [DestroyDocumentoVeiculoRecursoController::class,       'index'])->name('contratos.contratada.recurso.veiculo.destroy');
Route::delete('recurso/veiculo/destroy_veiculo/{veiculo}',              [DestroyVeiculoRecursoController::class,                'index'])->name('contratos.contratada.recurso.veiculo.destroy_veiculo');
Route::delete('recurso/veiculo/destroy_quilometragem/{quilometragem}',  [DestroyQuilometragemVeiculoRecursoController::class,   'index'])->name('contratos.contratada.recurso.veiculo.destroy_quilometragem');
