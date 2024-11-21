<?php

use App\Domain\Servico\MonAtpFauna\Configuracoes\MalhaViaria\app\Controller\EnviarFiscalController;
use App\Domain\Servico\MonAtpFauna\Configuracoes\MalhaViaria\app\Controller\IndexController;
use App\Domain\Servico\MonAtpFauna\Configuracoes\MalhaViaria\app\Controller\StoreShapeFileController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.mon_atp_fauna.configuracoes.malha_viaria.index');
Route::post('store-shapefile', StoreShapeFileController::class)->name('contratos.contratada.servicos.mon_atp_fauna.configuracoes.malha_viaria.store_shapefile');
Route::post('enviar-fiscal', EnviarFiscalController::class)->name('contratos.contratada.servicos.mon_atp_fauna.configuracoes.malha_viaria.send_fiscal');
