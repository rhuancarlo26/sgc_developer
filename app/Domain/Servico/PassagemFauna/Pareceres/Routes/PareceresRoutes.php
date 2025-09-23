<?php

use App\Domain\Servico\PassagemFauna\Pareceres\Controller\PareceresController;
use Illuminate\Support\Facades\Route;

Route::prefix('/Pareceres')->group(function () {
    Route::get('/listagem/{contrato}/{servico}',   [PareceresController::class,                'index'])->name('contratos.contratada.servicos.passagem.fauna.pareceres.index');
});
