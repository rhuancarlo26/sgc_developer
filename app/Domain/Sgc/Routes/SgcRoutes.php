<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Sgc\GestaoContrato\Controller\GetContratoDnitController;

Route::prefix('sgc')->group(function () {

    // Busca Contrato SIMDNIT
    Route::get('/contrato/{nr_contrato}', [GetContratoDnitController::class, 'getContrato'])->name('sgc.get_contrato');

    // Gestão de Contrato
    require __DIR__ . '/../GestaoContrato/Routes/GestaoContratosRoutes.php';

    require __DIR__ . '/../Contratada/Routes/ContratadaRoutes.php';
});
