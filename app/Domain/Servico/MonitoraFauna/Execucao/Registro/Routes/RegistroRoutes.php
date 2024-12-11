<?php

use App\Domain\Servico\MonitoraFauna\Execucao\Registro\Controller\{IndexController, CreateController};
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.execucao.registro.index');
Route::get('{contrato}/{servico}/create/{registro}', [CreateController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.execucao.registro.create');
