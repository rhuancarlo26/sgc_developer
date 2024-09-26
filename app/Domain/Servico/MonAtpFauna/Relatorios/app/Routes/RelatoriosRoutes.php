<?php

use App\Domain\Servico\MonAtpFauna\Relatorios\app\Controller\DeleteController;
use App\Domain\Servico\MonAtpFauna\Relatorios\app\Controller\EnviarFiscalController;
use App\Domain\Servico\MonAtpFauna\Relatorios\app\Controller\IndexController;
use App\Domain\Servico\MonAtpFauna\Relatorios\app\Controller\RelatorioController;
use App\Domain\Servico\MonAtpFauna\Relatorios\app\Controller\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}/', IndexController::class)->name('contratos.contratada.servicos.mon_atp_fauna.relatorios.index');
Route::post('/relatorio', RelatorioController::class)->name('contratos.contratada.servicos.mon_atp_fauna.relatorios.relatorio');
Route::post('/store', StoreController::class)->name('contratos.contratada.servicos.mon_atp_fauna.relatorios.store');
Route::patch('/update', StoreController::class)->name('contratos.contratada.servicos.mon_atp_fauna.relatorios.update');
Route::post('/enviar-fical', EnviarFiscalController::class)->name('contratos.contratada.servicos.mon_atp_fauna.relatorios.fiscal');
Route::delete('/{id}', DeleteController::class)->name('contratos.contratada.servicos.mon_atp_fauna.relatorios.delete');
