<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Contrato\Contratada\Recurso\Rh\Controller\CreateRhRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Rh\Controller\DestroyCurriculumController;
use App\Domain\Contrato\Contratada\Recurso\Rh\Controller\DestroyDocumentoBaixaRhRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Rh\Controller\DestroyDocumentoRhRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Rh\Controller\DestroyFotoPerfilController;
use App\Domain\Contrato\Contratada\Recurso\Rh\Controller\DestroyRhRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Rh\Controller\ListagemRhRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Rh\Controller\StoreCurriculumVitaeController;
use App\Domain\Contrato\Contratada\Recurso\Rh\Controller\StoreDocumentoBaixaRhRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Rh\Controller\StoreDocumentoRhRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Rh\Controller\StoreFotoPerfilController;
use App\Domain\Contrato\Contratada\Recurso\Rh\Controller\StoreRhRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Rh\Controller\UpdateRhRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Rh\Controller\VisualizarCurriculumVitaeController;
use App\Domain\Contrato\Contratada\Recurso\Rh\Controller\VisualizarDocumentoBaixaRhRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Rh\Controller\VisualizarDocumentoRhRecursoController;

//RH
Route::get('{contrato}/recurso/rh', [ListagemRhRecursoController::class, 'index'])->name('contratos.contratada.recurso.rh.index');
Route::get('{contrato}/recurso/rh/create/{rh?}', [CreateRhRecursoController::class, 'index'])->name('contratos.contratada.recurso.rh.create');
Route::post('recurso/rh/store', [StoreRhRecursoController::class, 'index'])->name('contratos.contratada.recurso.rh.store');
Route::post('recurso/rh/update', [UpdateRhRecursoController::class, 'index'])->name('contratos.contratada.recurso.rh.update');
Route::post('recurso/rh/store_documento', [StoreDocumentoRhRecursoController::class, 'index'])->name('contratos.contratada.recurso.rh.store_documento');
Route::post('recurso/rh/store_documento_curriculum', [StoreCurriculumVitaeController::class, 'index'])->name('contratos.contratada.recurso.rh.store_curriculum_pdf');
Route::post('recurso/rh/store_foto_perfil', [StoreFotoPerfilController::class, 'index'])->name('contratos.contratada.recurso.rh.store_foto_perfil');
Route::post('recurso/rh/store_documento_baixa', [StoreDocumentoBaixaRhRecursoController::class, 'index'])->name('contratos.contratada.recurso.rh.store_documento_baixa');
Route::get('recurso/rh/visualizar/{documento}', [VisualizarDocumentoRhRecursoController::class, 'index'])->name('contratos.contratada.recurso.rh.visualizar');
Route::get('recurso/rh/visualizar_curriculum/{rh}', [VisualizarCurriculumVitaeController::class, 'index'])->name('contratos.contratada.recurso.rh.visualizar_curriculum');
Route::get('recurso/rh/visualizar_documento_baixa/{documento_baixa}', [VisualizarDocumentoBaixaRhRecursoController::class, 'index'])->name('contratos.contratada.recurso.rh.visualizar_documento_baixa');
Route::delete('{contrato}/recurso/rh/destroy/{model_documento}', [DestroyDocumentoRhRecursoController::class, 'index'])->name('contratos.contratada.recurso.rh.destroy');
Route::delete('recurso/rh/destroy_curriculum/{rh}', [DestroyCurriculumController::class, 'index'])->name('contratos.contratada.recurso.rh.destroy_curriculum');
Route::delete('recurso/rh/destroy_foto_perfil/{rh}', [DestroyFotoPerfilController::class, 'index'])->name('contratos.contratada.recurso.rh.destroy_foto_perfil');
Route::delete('{contrato}/recurso/rh/destroy_documento_baixa/{model_documento}', [DestroyDocumentoBaixaRhRecursoController::class, 'index'])->name('contratos.contratada.recurso.rh.destroy_documento_baixa');
Route::delete('recurso/rh/destroy_rh/{rh}', [DestroyRhRecursoController::class, 'index'])->name('contratos.contratada.recurso.rh.destroy_rh');
