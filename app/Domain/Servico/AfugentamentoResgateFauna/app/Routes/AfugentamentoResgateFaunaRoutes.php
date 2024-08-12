<?php

use Illuminate\Support\Facades\Route;

Route::prefix('afugentamento-resgate-fauna')->group(function () {
    Route::prefix('/configuracao')->group(function () {
        require __DIR__ . '/../../Configuracao/VincularASV/Routes/VincularASVRoutes.php';
        require __DIR__ . '/../../Configuracao/VincularABIO/Routes/VincularABIORoutes.php';
    });

    Route::prefix('/execucao')->group(function () {
        require __DIR__ . '/../../Execucao/FrenteSupressao/Routes/FrenteSupressaoRoutes.php';
        require __DIR__ . '/../../Execucao/Registros/Routes/RegistrosRoutes.php';
    });
});
