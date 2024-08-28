<?php

use App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Controller\GetLicencaController;
use App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Controller\IndexController;
use App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Controller\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.mon_atp_fauna.execucao.registros.index');
Route::post('{contrato}/{servico}/store', [StoreController::class, 'index'])->name('contratos.contratada.servicos.mon_atp_fauna.execucao.registros.store');
Route::get('{contrato}/{servico}/get_licenca/{licenca}', [GetLicencaController::class, 'index'])->name('contratos.contratada.servicos.mon_atp_fauna.execucao.registros.get_licenca');
