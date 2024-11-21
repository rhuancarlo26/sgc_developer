<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Contrato\Contratada\Recurso\Equipamento\Controller\CreateEquipamentoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Equipamento\Controller\DestroyDocumentoEquipamentoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Equipamento\Controller\DestroyEquipamentoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Equipamento\Controller\ListagemEquipamentoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Equipamento\Controller\StoreDocumentoEquipamentoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Equipamento\Controller\StoreEquipamentoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Equipamento\Controller\UpdateEquipamentoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Equipamento\Controller\VisualizarDocumentoEquipamentoRecursoController;

//Equipamento
Route::get('{contrato}/recurso/equipamento', [ListagemEquipamentoRecursoController::class, 'index'])->name('contratos.contratada.recurso.equipamento.index');
Route::get('{contrato}/recurso/equipamento/create/{equipamento?}', [CreateEquipamentoRecursoController::class, 'index'])->name('contratos.contratada.recurso.equipamento.create');
Route::post('recurso/equipamento/store', [StoreEquipamentoRecursoController::class, 'index'])->name('contratos.contratada.recurso.equipamento.store');
Route::patch('recurso/equipamento/update', [UpdateEquipamentoRecursoController::class, 'index'])->name('contratos.contratada.recurso.equipamento.update');
Route::post('recurso/equipamento/store_documento', [StoreDocumentoEquipamentoRecursoController::class, 'index'])->name('contratos.contratada.recurso.equipamento.store_documento');
Route::get('recurso/equipamento/visualizar/{documento}', [VisualizarDocumentoEquipamentoRecursoController::class, 'index'])->name('contratos.contratada.recurso.equipamento.visualizar');
Route::delete('{contrato}/recurso/equipamento/destroy/{documento}', [DestroyDocumentoEquipamentoRecursoController::class, 'index'])->name('contratos.contratada.recurso.equipamento.destroy');
Route::delete('recurso/equipamento/destroy_equipamento/{equipamento}', [DestroyEquipamentoRecursoController::class, 'index'])->name('contratos.contratada.recurso.equipamento.destroy_equipamento');
