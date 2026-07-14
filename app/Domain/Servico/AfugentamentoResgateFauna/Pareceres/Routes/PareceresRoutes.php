<?php

use App\Domain\Servico\AfugentamentoResgateFauna\Pareceres\Controller\PareceresController;
use Illuminate\Support\Facades\Route;

Route::prefix('/Pareceres')->group(function () {
    Route::get('/listagem/{contrato}/{servico}',   [PareceresController::class,                'index'])->name('contratos.contratada.servicos.afugentamento.resgate.fauna.pareceres.index');
});
