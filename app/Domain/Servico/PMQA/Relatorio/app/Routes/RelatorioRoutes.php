<?php

use App\Domain\Servico\PMQA\Relatorio\app\Controller\IndexController;
use App\Domain\Servico\PMQA\Relatorio\app\Controller\StoreController;
use App\Domain\Servico\PMQA\Relatorio\app\Controller\UpdateController;
use App\Domain\Servico\PMQA\Relatorio\app\Controller\DeleteController;
use App\Domain\Servico\PMQA\Relatorio\app\Controller\PdfController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}', [IndexController::class, 'index'])->name('contratos.contratada.servicos.pmqa.relatorio.index');
Route::post('{contrato}/{servico}/store', [StoreController::class, 'index'])->name('contratos.contratada.servicos.pmqa.relatorio.store');
Route::post('{contrato}/{servico}/update', [UpdateController::class, 'index'])->name('contratos.contratada.servicos.pmqa.relatorio.update');
Route::delete('{contrato}/{servico}/delete/{relatorio}', [DeleteController::class, 'index'])->name('contratos.contratada.servicos.pmqa.relatorio.delete');
Route::get('{contrato}/{servico}/gerar_pdf/{relatorio}', [PdfController::class, 'index'])->name('contratos.contratada.servicos.pmqa.relatorio.gerar_pdf');
