<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Servico\PassagemFauna\Execucao\Registro\Controller\IndexController;
use App\Domain\Servico\PassagemFauna\Execucao\Registro\Controller\CreateController;
use App\Domain\Servico\PassagemFauna\Execucao\Registro\Controller\StoreController;
use App\Domain\Servico\PassagemFauna\Execucao\Registro\Controller\UpdateController;
use App\Domain\Servico\PassagemFauna\Execucao\Registro\Controller\StoreArquivoController;
use App\Domain\Servico\PassagemFauna\Execucao\Registro\Controller\DeleteImagemController;
use App\Domain\Servico\PassagemFauna\Execucao\Registro\Controller\VisualizarImagemController;
use App\Domain\Servico\PassagemFauna\Execucao\Registro\Controller\DeleteController;

Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.execucao.registro.index');
Route::get('{contrato}/{servico}/create/{registro?}', [CreateController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.execucao.registro.create');
Route::post('{contrato}/{servico}/store', [StoreController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.execucao.registro.store');
Route::post('{contrato}/{servico}/store_arquivo', [StoreArquivoController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.execucao.registro.store_arquivo');
Route::post('{contrato}/{servico}/update', [UpdateController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.execucao.registro.update');
Route::delete('{contrato}/{servico}/delete/{registro}', [DeleteController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.execucao.registro.delete');
Route::delete('{contrato}/{servico}/delete_imagem/{imagem}', [DeleteImagemController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.execucao.registro.delete_imagem');
Route::get('{contrato}/{servico}/visualizar_imagem/{imagem}', [VisualizarImagemController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.execucao.registro.visualizar_imagem');
