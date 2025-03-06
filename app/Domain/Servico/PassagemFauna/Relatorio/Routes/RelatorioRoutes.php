<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Servico\PassagemFauna\Relatorio\Controller\IndexController;
use App\Domain\Servico\PassagemFauna\Relatorio\Controller\StoreController;
use App\Domain\Servico\PassagemFauna\Relatorio\Controller\UpdateController;
use App\Domain\Servico\PassagemFauna\Relatorio\Controller\DeleteController;
use App\Domain\Servico\PassagemFauna\Relatorio\Controller\PdfController;

Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.relatorio.index');
Route::post('{contrato}/{servico}/store', [StoreController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.relatorio.store');
Route::post('{contrato}/{servico}/update', [UpdateController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.relatorio.update');
Route::delete('{contrato}/{servico}/delete/{relatorio}', [DeleteController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.relatorio.delete');
Route::get('{contrato}/{servico}/gerar_pdf/{relatorio}', [PdfController::class, 'index'])->name('contratos.contratada.servicos.passagem_fauna.relatorio.gerar_pdf');
