<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Servico\PassagemFauna\Configuracao\PassagemFauna\Controller\IndexController;
use App\Domain\Servico\PassagemFauna\Configuracao\PassagemFauna\Controller\DownloadModeloController;
use App\Domain\Servico\PassagemFauna\Configuracao\PassagemFauna\Controller\ImportarPassagemFaunaController;

Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.configuracao.passagem_fauna.index');
Route::get('{contrato}/{servico}/download_modelo', [DownloadModeloController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.configuracao.passagem_fauna.download_modelo');
Route::post('{contrato}/{servico}/importar', [ImportarPassagemFaunaController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.configuracao.passagem_fauna.importar');
