<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Servico\PassagemFauna\Execucao\Campanha\Controller\IndexController;
use App\Domain\Servico\PassagemFauna\Execucao\Campanha\Controller\CreateController;
use App\Domain\Servico\PassagemFauna\Execucao\Campanha\Controller\StoreController;
use App\Domain\Servico\PassagemFauna\Execucao\Campanha\Controller\StoreAbioController;
use App\Domain\Servico\PassagemFauna\Execucao\Campanha\Controller\DeleteAbioController;
use App\Domain\Servico\PassagemFauna\Execucao\Campanha\Controller\DeleteRhController;
use App\Domain\Servico\PassagemFauna\Execucao\Campanha\Controller\StoreRhController;
use App\Domain\Servico\PassagemFauna\Execucao\Campanha\Controller\StoreRetController;
use App\Domain\Servico\PassagemFauna\Execucao\Campanha\Controller\DeleteRetController;
use App\Domain\Servico\PassagemFauna\Execucao\Campanha\Controller\UpdateController;
use App\Domain\Servico\PassagemFauna\Execucao\Campanha\Controller\DeleteController;

Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.execucao.campanha.index');
Route::get('{contrato}/{servico}/create/{campanha?}', [CreateController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.execucao.campanha.create');
Route::post('{contrato}/{servico}/store', [StoreController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.execucao.campanha.store');
Route::post('{contrato}/{servico}/update', [UpdateController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.execucao.campanha.update');
Route::post('{contrato}/{servico}/store_abio', [StoreAbioController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.execucao.campanha.store_abio');
Route::post('{contrato}/{servico}/store_rh', [StoreRhController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.execucao.campanha.store_rh');
Route::post('{contrato}/{servico}/store_ret', [StoreRetController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.execucao.campanha.store_ret');
Route::delete('{contrato}/{servico}/delete/{campanha}', [DeleteController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.execucao.campanha.delete');
Route::delete('{contrato}/{servico}/delete_abio/{campanha_abio}', [DeleteAbioController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.execucao.campanha.delete_abio');
Route::delete('{contrato}/{servico}/delete_rh/{campanha_rh}', [DeleteRhController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.execucao.campanha.delete_rh');
Route::delete('{contrato}/{servico}/delete_ret/{campanha_ret}', [DeleteRetController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.execucao.campanha.delete_ret');
