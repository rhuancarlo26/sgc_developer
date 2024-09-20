<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Servico\PassagemFauna\Configuracao\VincularABIO\Controller\IndexController;
use App\Domain\Servico\PassagemFauna\Configuracao\VincularABIO\Controller\BuscarLicencaController;
use App\Domain\Servico\PassagemFauna\Configuracao\VincularABIO\Controller\StoreController;
use App\Domain\Servico\PassagemFauna\Configuracao\VincularABIO\Controller\StoreRETController;
use App\Domain\Servico\PassagemFauna\Configuracao\VincularABIO\Controller\DeleteRETController;
use App\Domain\Servico\PassagemFauna\Configuracao\VincularABIO\Controller\VisualizarAbioRetController;
use App\Domain\Servico\PassagemFauna\Configuracao\VincularABIO\Controller\DeleteABIOController;

Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.configuracao.vincular_abio.index');
Route::post('{contrato}/{servico}/buscar_licenca', [BuscarLicencaController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.configuracao.vincular_abio.buscar_licenca');
Route::post('{contrato}/{servico}/store', [StoreController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.configuracao.vincular_abio.store');
Route::post('{contrato}/{servico}/store_ret', [StoreRETController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.configuracao.vincular_abio.store_ret');
Route::delete('{contrato}/{servico}/delete_ret/{abio_ret}', [DeleteRETController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.configuracao.vincular_abio.delete_ret');
Route::get('{contrato}/{servico}/visualizar_ret/{abio_ret}', [VisualizarAbioRetController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.configuracao.vincular_abio.visualizar_ret');
Route::delete('{contrato}/{servico}/delete_abio/{abio}', [DeleteABIOController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.configuracao.vincular_abio.delete_abio');
