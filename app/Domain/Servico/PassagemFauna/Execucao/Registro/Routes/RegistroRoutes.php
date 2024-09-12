<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Servico\PassagemFauna\Execucao\Registro\Controller\IndexController;
use App\Domain\Servico\PassagemFauna\Execucao\Registro\Controller\CreateController;
use App\Domain\Servico\PassagemFauna\Execucao\Registro\Controller\StoreController;
use App\Domain\Servico\PassagemFauna\Execucao\Registro\Controller\UpdateController;

Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.execucao.registro.index');
Route::get('{contrato}/{servico}/create/{registro?}', [CreateController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.execucao.registro.create');
Route::post('{contrato}/{servico}/store', [StoreController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.execucao.registro.store');
Route::post('{contrato}/{servico}/update', [UpdateController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.execucao.registro.update');
