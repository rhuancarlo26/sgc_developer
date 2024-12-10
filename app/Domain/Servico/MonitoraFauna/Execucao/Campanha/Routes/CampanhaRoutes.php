<?php

use App\Domain\Servico\MonitoraFauna\Execucao\Campanha\Controller\CreateController;
use App\Domain\Servico\MonitoraFauna\Execucao\Campanha\Controller\DeleteAbioController;
use App\Domain\Servico\MonitoraFauna\Execucao\Campanha\Controller\DeleteController;
use App\Domain\Servico\MonitoraFauna\Execucao\Campanha\Controller\DeleteProfissionalController;
use App\Domain\Servico\MonitoraFauna\Execucao\Campanha\Controller\IndexController;
use App\Domain\Servico\MonitoraFauna\Execucao\Campanha\Controller\StoreAbioController;
use App\Domain\Servico\MonitoraFauna\Execucao\Campanha\Controller\StoreController;
use App\Domain\Servico\MonitoraFauna\Execucao\Campanha\Controller\StoreProfissionalController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.execucao.campanha.index');
Route::get('{contrato}/{servico}/create/{campanha?}', [CreateController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.execucao.campanha.create');
Route::post('{contrato}/{servico}/store', [StoreController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.execucao.campanha.store');
Route::delete('{contrato}/{servico}/delete/{campanha}', [DeleteController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.execucao.campanha.delete');
Route::post('{contrato}/{servico}/store_abio', [StoreAbioController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.execucao.campanha.store_abio');
Route::delete('{contrato}/{servico}/delete_abio/{campanha_abio}', [DeleteAbioController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.execucao.campanha.delete_abio');
Route::post('{contrato}/{servico}/store_profissional', [StoreProfissionalController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.execucao.campanha.store_profissional');
Route::delete('{contrato}/{servico}/delete_profissional/{campanha_profissional}', [DeleteProfissionalController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.execucao.campanha.delete_profissional');
