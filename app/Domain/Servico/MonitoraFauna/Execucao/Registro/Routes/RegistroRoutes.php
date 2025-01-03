<?php

use App\Domain\Servico\MonitoraFauna\Execucao\Registro\Controller\{IndexController, CreateController, StoreController, DeleteController, UpdateController};
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.execucao.registro.index');
Route::get('{contrato}/{servico}/create/{registro?}', [CreateController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.execucao.registro.create');
Route::post('{contrato}/{servico}/store', [StoreController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.execucao.registro.store');
Route::post('{contrato}/{servico}/update', [UpdateController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.execucao.registro.update');
Route::delete('{contrato}/{servico}/delete/{registro}', [DeleteController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.execucao.registro.delete');
