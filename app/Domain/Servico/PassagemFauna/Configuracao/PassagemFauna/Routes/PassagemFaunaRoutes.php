<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Servico\PassagemFauna\Configuracao\PassagemFauna\Controller\IndexController;
use App\Domain\Servico\PassagemFauna\Configuracao\PassagemFauna\Controller\DownloadModeloController;
use App\Domain\Servico\PassagemFauna\Configuracao\PassagemFauna\Controller\ImportarPassagemFaunaController;
use App\Domain\Servico\PassagemFauna\Configuracao\PassagemFauna\Controller\DeleteController;
use App\Domain\Servico\PassagemFauna\Configuracao\PassagemFauna\Controller\UpdateController;
use App\Domain\Servico\PassagemFauna\Configuracao\PassagemFauna\Controller\SubmeterFiscalController;

Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.configuracao.passagem_fauna.index');
Route::get('{contrato}/{servico}/download_modelo', [DownloadModeloController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.configuracao.passagem_fauna.download_modelo');
Route::post('{contrato}/{servico}/importar', [ImportarPassagemFaunaController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.configuracao.passagem_fauna.importar');
Route::post('{contrato}/{servico}/update', [UpdateController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.configuracao.passagem_fauna.update');
Route::delete('{contrato}/{servico}/delete/{passagem_fauna}', [DeleteController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.configuracao.passagem_fauna.delete');
Route::post('{contrato}/{servico}/submeter_fiscal', [SubmeterFiscalController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.configuracao.passagem_fauna.submeter_fiscal');
