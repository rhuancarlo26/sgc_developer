<?php

use App\Domain\Servico\MonAtpFauna\Relatorios\app\Controller\GetLicencaController;
use App\Domain\Servico\MonAtpFauna\Relatorios\app\Controller\IndexController;
use App\Domain\Servico\MonAtpFauna\Relatorios\app\Controller\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}/',                         [IndexController::class,        'index'])->name('contratos.contratada.servicos.mon_atp_fauna.relatorios.index');
Route::post('{contrato}/{servico}/store',                   [StoreController::class,        'index'])->name('contratos.contratada.servicos.mon_atp_fauna.relatorios.store');
Route::get('{contrato}/{servico}/get_licenca/{licenca}',    [GetLicencaController::class,   'index'])->name('contratos.contratada.servicos.mon_atp_fauna.relatorios.get_licenca');
