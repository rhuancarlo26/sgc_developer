<?php

use App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Controller\DeleteController;
use App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Controller\DeleteImagemController;
use App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Controller\ExcelExportController;
use App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Controller\GetImagensController;
use App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Controller\IndexController;
use App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Controller\StoreController;
use App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Controller\UpdateController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.mon_atp_fauna.execucao.registros.index');
Route::get('/{registro}', GetImagensController::class)->name('contratos.contratada.servicos.mon_atp_fauna.execucao.registros.imagens');
Route::post('/store', StoreController::class)->name('contratos.contratada.servicos.mon_atp_fauna.execucao.registros.store');
Route::patch('/update', UpdateController::class)->name('contratos.contratada.servicos.mon_atp_fauna.execucao.registros.update');
Route::delete('/{registro}', DeleteController::class)->name('contratos.contratada.servicos.mon_atp_fauna.execucao.registros.delete');
Route::delete('/imagem/{imagem}', DeleteImagemController::class)->name('contratos.contratada.servicos.mon_atp_fauna.execucao.registros.imagens-delete');
Route::get('/excel/{servico}/exportar',         ExcelExportController::class)->name('contratos.contratada.servicos.mon_atp_fauna.execucao.registros.export');

