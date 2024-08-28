<?php

use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Controller\GetLicencaController;
use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Controller\IndexController;
use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Controller\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}/',                         [IndexController::class,        'index'])->name('contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.index');
Route::post('{contrato}/{servico}/store',                   [StoreController::class,        'index'])->name('contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.store');
Route::get('{contrato}/{servico}/get_licenca/{licenca}',    [GetLicencaController::class,   'index'])->name('contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.get_licenca');
