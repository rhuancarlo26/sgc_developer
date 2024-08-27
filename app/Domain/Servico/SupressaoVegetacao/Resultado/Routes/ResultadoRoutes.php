<?php

use App\Domain\Servico\SupressaoVegetacao\Resultado\Controller\DeleteController;
use App\Domain\Servico\SupressaoVegetacao\Resultado\Controller\DestinacaoAnaliseController;
use App\Domain\Servico\SupressaoVegetacao\Resultado\Controller\IndexController;
use App\Domain\Servico\SupressaoVegetacao\Resultado\Controller\PilhaAnaliseController;
use App\Domain\Servico\SupressaoVegetacao\Resultado\Controller\StoreAnaliseController;
use App\Domain\Servico\SupressaoVegetacao\Resultado\Controller\StoreController;
use App\Domain\Servico\SupressaoVegetacao\Resultado\Controller\SupressaoAnaliseController;
use App\Domain\Servico\SupressaoVegetacao\Resultado\Controller\UpdateController;
use Illuminate\Support\Facades\Route;

Route::get('/{contrato}/{servico}',                              IndexController::class)->name('contratos.contratada.servicos.supressao-vegetacao.resultado.index');
Route::get('/anlise-resultado/{servico}/{resultado}',            SupressaoAnaliseController::class)->name('contratos.contratada.servicos.supressao-vegetacao.resultado.supressao-analise');
Route::get('/anlise-resultado/{servico}/{resultado}/pilha',      PilhaAnaliseController::class)->name('contratos.contratada.servicos.supressao-vegetacao.resultado.pilha-analise');
Route::get('/anlise-resultado/{servico}/{resultado}/destinacao', DestinacaoAnaliseController::class)->name('contratos.contratada.servicos.supressao-vegetacao.resultado.destinacao-analise');
Route::post('/store',                                            StoreController::class)->name('contratos.contratada.servicos.supressao-vegetacao.resultado.store');
Route::post('/store-analise',                                    StoreAnaliseController::class)->name('contratos.contratada.servicos.supressao-vegetacao.resultado.store-analisar');
Route::patch('/update',                                          UpdateController::class)->name('contratos.contratada.servicos.supressao-vegetacao.resultado.update');
Route::delete('{resultado}/delete',                              DeleteController::class)->name('contratos.contratada.servicos.supressao-vegetacao.resultado.delete');

