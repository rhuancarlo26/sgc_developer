<?php

use App\Domain\Servico\MonAtpFauna\Configuracoes\VincualarABIO\app\Controller\DeleteController;
use App\Domain\Servico\MonAtpFauna\Configuracoes\VincualarABIO\app\Controller\DeleteRetController;
use App\Domain\Servico\MonAtpFauna\Configuracoes\VincualarABIO\app\Controller\GetLicencaController;
use App\Domain\Servico\MonAtpFauna\Configuracoes\VincualarABIO\app\Controller\IndexController;
use App\Domain\Servico\MonAtpFauna\Configuracoes\VincualarABIO\app\Controller\StoreController;
use App\Domain\Servico\MonAtpFauna\Configuracoes\VincualarABIO\app\Controller\StoreRetController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.mon_atp_fauna.configuracoes.vincular_abio.index');
Route::post('{contrato}/{servico}/store', [StoreController::class, 'index'])->name('contratos.contratada.servicos.mon_atp_fauna.configuracoes.vincular_abio.store');
Route::get('{contrato}/{servico}/get_licenca/{licenca}', [GetLicencaController::class, 'index'])->name('contratos.contratada.servicos.mon_atp_fauna.configuracoes.vincular_abio.get_licenca');
Route::post('store-ret', StoreRetController::class)->name('contratos.contratada.servicos.mon_atp_fauna.configuracoes.vincular_abio.store_ret');
Route::delete('{ret}', DeleteRetController::class)->name('contratos.contratada.servicos.mon_atp_fauna.configuracoes.vincular_abio.delete_ret');
Route::delete('vinculacao/{id}', DeleteController::class)->name('contratos.contratada.servicos.mon_atp_fauna.configuracoes.vincular_abio.delete');
