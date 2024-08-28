<?php

use Illuminate\Support\Facades\Route;

use App\Domain\Servico\ControleDeOcorrencias\Configuracoes\LoteDeObra\Controller\IndexController;

Route::prefix('/lotedeobra')->group(function () {
  Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.controledeocorrencias.configuracoes.lotedeobra.index');
});