<?php

use Illuminate\Support\Facades\Route;

use App\Domain\Servico\ControleDeOcorrencias\Configuracoes\Empreendimento\Controller\IndexController;

Route::prefix('/empreendimento')->group(function () {
  Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.controledeocorrencias.configuracoes.empreendimento.index');
});
