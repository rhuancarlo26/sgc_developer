<?php

use Illuminate\Support\Facades\Route;

Route::prefix('modulos')->name('modulos.')->group(function () {

    // ConfiguracaoModulos
    require __DIR__ . '/../ConfiguracoesModulos/Routes/ConfiguracoesModulosRoutes.php';

    // Importador do modulo dinamico
    require __DIR__ . '/../Importador/Routes/ImportadorRoutes.php';
});
