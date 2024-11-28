<?php

use App\Domain\Servico\MonitoraFauna\Configuracao\VincularABIO\Controller\GetLicencaController;
use App\Domain\Servico\MonitoraFauna\Configuracao\VincularABIO\Controller\IndexController;
use App\Domain\Servico\MonitoraFauna\Configuracao\VincularABIO\Controller\StoreController;
use App\Domain\Servico\MonitoraFauna\Configuracao\VincularABIO\Controller\StoreRetController;
use App\Domain\Servico\MonitoraFauna\Configuracao\VincularABIO\Controller\VisualizarABIORetController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.configuracoes.vincular_abio.index');
Route::get('{contrato}/{servico}/get_licenca/{licenca}', [GetLicencaController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.configuracoes.vincular_abio.get_licenca');
Route::post('{contrato}/{servico}/store', [StoreController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.configuracoes.vincular_abio.store');
Route::post('{contrato}/{servico}/store_ret', [StoreRetController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.configuracoes.vincular_abio.store_ret');
Route::get('{contrato}/{servico}/visualizar_abio_ret/{abio_ret}', [VisualizarABIORetController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.configuracoes.vincular_abio.visualizar_abio_ret');
