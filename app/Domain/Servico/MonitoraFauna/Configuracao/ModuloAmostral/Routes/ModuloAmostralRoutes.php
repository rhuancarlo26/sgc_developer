<?php

use App\Domain\Servico\MonitoraFauna\Configuracao\ModuloAmostral\Controller\IndexController;
use App\Domain\Servico\MonitoraFauna\Configuracao\ModuloAmostral\Controller\StoreController;
use App\Domain\Servico\MonitoraFauna\Configuracao\ModuloAmostral\Controller\UpdateController;
use App\Domain\Servico\MonitoraFauna\Configuracao\ModuloAmostral\Controller\DeleteController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.configuracoes.modulo_amostral.index');
Route::post('{contrato}/{servico}/store', [StoreController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.configuracoes.modulo_amostral.store');
Route::post('{contrato}/{servico}/update', [UpdateController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.configuracoes.modulo_amostral.update');
Route::delete('{contrato}/{servico}/delete/{modulo}', [DeleteController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.configuracoes.modulo_amostral.delete');
