<?php

use App\Domain\Servico\MonitoraFauna\Resultado\Controller\{IndexController, StoreController, UpdateController, DestroyController};
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.resultado.index');
Route::post('{contrato}/{servico}/store', [StoreController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.resultado.store');
Route::post('{contrato}/{servico}/update', [UpdateController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.resultado.update');
Route::delete('{contrato}/{servico}/destroy/{resultado}', [DestroyController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.resultado.destroy');
