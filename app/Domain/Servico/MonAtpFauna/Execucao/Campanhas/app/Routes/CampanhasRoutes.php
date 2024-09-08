<?php

use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Controller\DeleteAbioController;
use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Controller\DeleteController;
use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Controller\DeleteRetController;
use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Controller\GetAbioController;
use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Controller\GetRetCampanhaController;
use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Controller\GetRetVinculadaController;
use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Controller\IndexController;
use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Controller\StoreController;
use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Controller\UpdateController;
use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Controller\VincularAbioController;
use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Controller\VincularRetController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.index');
Route::post('store', StoreController::class)->name('contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.store');
Route::patch('update', UpdateController::class)->name('contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.update');
Route::delete('delete/{campanha}', DeleteController::class)->name('contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.delete');
Route::get('get-abio', GetAbioController::class)->name('contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.get-abio');
Route::get('get-rets-vinculadas', GetRetVinculadaController::class)->name('contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.get-rets');
Route::get('get-rets-campanha', GetRetCampanhaController::class)->name('contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.get-rets-campanha');
Route::post('vincular-abio', VincularAbioController::class)->name('contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.vincular-abio');
Route::post('vincular-ret', VincularRetController::class)->name('contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.vincular-ret');
Route::delete('delete-abio/{id}', DeleteAbioController::class)->name('contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.delete-abio');
Route::delete('delete-rets/{id}', DeleteRetController::class)->name('contratos.contratada.servicos.mon_atp_fauna.execucao.campanhas.delete-ret');
