<?php

use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\VinculacaoPonto\Controller\DeleteController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\VinculacaoPonto\Controller\EnviarListaFiscalController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\VinculacaoPonto\Controller\IndexController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\VinculacaoPonto\Controller\StoreController;
use Illuminate\Support\Facades\Route;

Route::prefix('/vinculacao_ponto')->group(function () {
  Route::get('{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.pmqa.configuracao.vinculacao_ponto.index');
  Route::post('{servico}/store', [StoreController::class, 'index'])->name('contratos.contratada.servicos.pmqa.configuracao.vinculacao_ponto.store');
  Route::delete('{servico}/destroy/{lista}', [DeleteController::class, 'index'])->name('contratos.contratada.servicos.pmqa.configuracao.vinculacao_ponto.destroy');
  Route::post('{servico}/enviar_lista_fiscal', [EnviarListaFiscalController::class, 'index'])->name('contratos.contratada.servicos.pmqa.configuracao.vinculacao_ponto.enviar_lista_fiscal');
});
