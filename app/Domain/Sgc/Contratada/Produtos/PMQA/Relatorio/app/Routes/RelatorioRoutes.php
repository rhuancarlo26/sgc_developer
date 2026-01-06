<?php

use App\Domain\Sgc\Contratada\Produtos\PMQA\Relatorio\app\Controller\IndexController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Relatorio\app\Controller\StoreController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Relatorio\app\Controller\UpdateController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Relatorio\app\Controller\DeleteController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Relatorio\app\Controller\PdfController;
use Illuminate\Support\Facades\Route;

Route::get('{servico}', [IndexController::class, 'index'])->name('contratos.contratada.servicos.pmqa.relatorio.index');
Route::post('{servico}/store', [StoreController::class, 'index'])->name('contratos.contratada.servicos.pmqa.relatorio.store');
Route::post('{servico}/update', [UpdateController::class, 'index'])->name('contratos.contratada.servicos.pmqa.relatorio.update');
Route::delete('{servico}/delete/{relatorio}', [DeleteController::class, 'index'])->name('contratos.contratada.servicos.pmqa.relatorio.delete');
Route::get('{servico}/gerar_pdf/{relatorio}', [PdfController::class, 'index'])->name('contratos.contratada.servicos.pmqa.relatorio.gerar_pdf');
