<?php

use App\Domain\Servico\MonAtpFauna\Configuracoes\MalhaViaria\app\Controller\GetLicencaController;
use App\Domain\Servico\MonAtpFauna\Configuracoes\MalhaViaria\app\Controller\IndexController;
use App\Domain\Servico\MonAtpFauna\Configuracoes\MalhaViaria\app\Controller\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}/',                         [IndexController::class,        'index'])->name('contratos.contratada.servicos.mon_atp_fauna.configuracoes.malha_viaria.index');
Route::post('{contrato}/{servico}/store',                   [StoreController::class,        'index'])->name('contratos.contratada.servicos.mon_atp_fauna.configuracoes.malha_viaria.store');
Route::get('{contrato}/{servico}/get_licenca/{licenca}',    [GetLicencaController::class,   'index'])->name('contratos.contratada.servicos.mon_atp_fauna.configuracoes.malha_viaria.get_licenca');
