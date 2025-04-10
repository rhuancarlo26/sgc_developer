<?php

use App\Domain\Servico\MonAtpFauna\Resultado\app\Controller\DeleteCampanhaController;
use App\Domain\Servico\MonAtpFauna\Resultado\app\Controller\DeleteController;
use App\Domain\Servico\MonAtpFauna\Resultado\app\Controller\GetCampanhaController;
use App\Domain\Servico\MonAtpFauna\Resultado\app\Controller\IndexController;
use App\Domain\Servico\MonAtpFauna\Resultado\app\Controller\ResultadoAnaliseController;
use App\Domain\Servico\MonAtpFauna\Resultado\app\Controller\StoreCampanhaController;
use App\Domain\Servico\MonAtpFauna\Resultado\app\Controller\StoreController;
use App\Domain\Servico\MonAtpFauna\Resultado\app\Controller\UpdateController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}/',                         [IndexController::class,        'index'])->name('contratos.contratada.servicos.mon_atp_fauna.resultado.index');
Route::get('resultado/{resultado}/analise', ResultadoAnaliseController::class)->name('contratos.contratada.servicos.mon_atp_fauna.resultado.analise');
Route::post('/store', StoreController::class)->name('contratos.contratada.servicos.mon_atp_fauna.resultado.store');
Route::patch('/update', UpdateController::class)->name('contratos.contratada.servicos.mon_atp_fauna.resultado.update');
Route::post('/store-campanha', StoreCampanhaController::class)->name('contratos.contratada.servicos.mon_atp_fauna.resultado.store-campanha');
Route::get('/get/{id}/campanhas', GetCampanhaController::class)->name('contratos.contratada.servicos.mon_atp_fauna.resultado.get-campanhas');
Route::delete('/{id}/campanhas', DeleteCampanhaController::class)->name('contratos.contratada.servicos.mon_atp_fauna.resultado.delete-campanha');
Route::delete('/{id}/campanhas', DeleteController::class)->name('contratos.contratada.servicos.mon_atp_fauna.resultado.delete');
