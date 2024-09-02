<?php

use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Controller\DeleteAbioController;
use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Controller\GetAbioController;
use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Controller\IndexController;
use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Controller\StoreController;
use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Controller\UpdateController;
use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Controller\VincularAbioController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.index');
Route::post('store', StoreController::class)->name('contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.store');
Route::patch('update', UpdateController::class)->name('contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.update');
Route::get('get-abio', GetAbioController::class)->name('contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.get-abio');
Route::post('vincular-abio', VincularAbioController::class)->name('contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.vincular-abio');
Route::delete('delete-abio/{id}', DeleteAbioController::class)->name('contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.delete-abio');
