<?php

use App\Domain\Sgc\Contratada\Produtos\PMQA\Relatorio\app\Controller\IndexController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Relatorio\app\Controller\StoreController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Relatorio\app\Controller\UpdateController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Relatorio\app\Controller\DeleteController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Relatorio\app\Controller\PdfController;
use Illuminate\Support\Facades\Route;

Route::prefix('/relatorio')->group(function () {
    Route::get('{pmqa}',                       [IndexController::class,   'index'])->name('contratos.contratada.relatorio.pmqa.relatorio.index');
    Route::post('{pmqa}/store',                [StoreController::class,   'index'])->name('contratos.contratada.relatorio.pmqa.relatorio.store');
    Route::post('{pmqa}/update',               [UpdateController::class,  'index'])->name('contratos.contratada.relatorio.pmqa.relatorio.update');
    Route::delete('{pmqa}/delete/{relatorio}', [DeleteController::class,  'index'])->name('contratos.contratada.relatorio.pmqa.relatorio.delete');
    Route::get('{pmqa}/gerar_pdf/{relatorio}', [PdfController::class,     'index'])->name('contratos.contratada.relatorio.pmqa.relatorio.gerar_pdf');
});
