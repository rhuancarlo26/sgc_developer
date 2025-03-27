<?php

use App\Domain\Servico\MonitoraFauna\Resultado\Controller\{IndexController, ResultadoController, StoreController, UpdateController, DestroyController};
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.resultado.index');
Route::get('{contrato}/{servico}/resultado/{resultado}', [ResultadoController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.resultado.resultado');
Route::post('{contrato}/{servico}/store', [StoreController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.resultado.store');
Route::post('{contrato}/{servico}/update', [UpdateController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.resultado.update');
Route::delete('{contrato}/{servico}/destroy/{resultado}', [DestroyController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.resultado.destroy');
