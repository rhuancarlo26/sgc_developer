<?php

use App\Domain\Servico\MonAtpFauna\Resultado\app\Controller\GetLicencaController;
use App\Domain\Servico\MonAtpFauna\Resultado\app\Controller\IndexController;
use App\Domain\Servico\MonAtpFauna\Resultado\app\Controller\ResultadoAnaliseController;
use App\Domain\Servico\MonAtpFauna\Resultado\app\Controller\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}/',                         [IndexController::class,        'index'])->name('contratos.contratada.servicos.mon_atp_fauna.resultado.index');
Route::get('resultado/{resultado}/analise', ResultadoAnaliseController::class)->name('contratos.contratada.servicos.mon_atp_fauna.resultado.analise');
Route::post('/store', ResultadoAnaliseController::class)->name('contratos.contratada.servicos.mon_atp_fauna.resultado.store');
