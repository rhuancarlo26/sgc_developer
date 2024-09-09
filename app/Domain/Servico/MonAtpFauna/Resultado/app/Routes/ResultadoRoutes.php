<?php

use App\Domain\Servico\MonAtpFauna\Resultado\app\Controller\GetLicencaController;
use App\Domain\Servico\MonAtpFauna\Resultado\app\Controller\IndexController;
use App\Domain\Servico\MonAtpFauna\Resultado\app\Controller\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}/',                         [IndexController::class,        'index'])->name('contratos.contratada.servicos.mon_atp_fauna.resultado.index');
Route::post('{contrato}/{servico}/store',                   [StoreController::class,        'index'])->name('contratos.contratada.servicos.mon_atp_fauna.resultado.store');
Route::get('{contrato}/{servico}/get_licenca/{licenca}',    [GetLicencaController::class,   'index'])->name('contratos.contratada.servicos.mon_atp_fauna.resultado.get_licenca');
