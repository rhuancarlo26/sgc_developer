<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Servico\PassagemFauna\Resultado\Controller\IndexController;
use App\Domain\Servico\PassagemFauna\Resultado\Controller\StoreController;
use App\Domain\Servico\PassagemFauna\Resultado\Controller\UpdateController;
use App\Domain\Servico\PassagemFauna\Resultado\Controller\DeleteController;
use App\Domain\Servico\PassagemFauna\Resultado\Controller\ResultadoController;
use App\Domain\Servico\PassagemFauna\Resultado\Controller\StoreAnaliseController;
use App\Domain\Servico\PassagemFauna\Resultado\Controller\UpdateAnaliseController;
use App\Domain\Servico\PassagemFauna\Resultado\Controller\StoreOutraAnaliseController;
use App\Domain\Servico\PassagemFauna\Resultado\Controller\VisualizarOutraAnaliseController;
use App\Domain\Servico\PassagemFauna\Resultado\Controller\DeleteOutraAnaliseController;
use App\Domain\Servico\PassagemFauna\Resultado\Controller\UpdateOutraAnaliseController;

Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.resultado.index');
Route::get('{contrato}/{servico}/resultado/{resultado}', [ResultadoController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.resultado.resultado');
Route::post('{contrato}/{servico}/store', [StoreController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.resultado.store');
Route::post('{contrato}/{servico}/{resultado}/store_analise', [StoreAnaliseController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.resultado.store_analise');
Route::post('{contrato}/{servico}/update', [UpdateController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.resultado.update');
Route::post('{contrato}/{servico}/{resultado}/store_outra_analise', [StoreOutraAnaliseController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.resultado.store_outra_analise');
Route::post('{contrato}/{servico}/{resultado}/update_outra_analise', [UpdateOutraAnaliseController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.resultado.update_outra_analise');
Route::get('{contrato}/{servico}/{resultado}/visualizar_outra_analise/{outra_analise}', [VisualizarOutraAnaliseController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.resultado.visualizar_outra_analise');
Route::post('{contrato}/{servico}/{resultado}/update_analise', [UpdateAnaliseController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.resultado.update_analise');
Route::delete('{contrato}/{servico}/delete/{resultado}', [DeleteController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.resultado.delete');
Route::delete('{contrato}/{servico}/{resultado}/delete_outra_analise/{outra_analise}', [DeleteOutraAnaliseController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.resultado.delete_outra_analise');
