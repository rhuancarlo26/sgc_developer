<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/passagem_fauna')->group(function () {
    Route::prefix('/configuracao')->group(function () {
        Route::prefix('/vincular_abio')->group(function () {
            require __DIR__ . '/../../Configuracao/VincularABIO/Routes/VincularABIORoutes.php';
        });
        Route::prefix('/passagem_fauna')->group(function () {
            require __DIR__ . '/../../Configuracao/PassagemFauna/Routes/PassagemFaunaRoutes.php';
        });
    });
    Route::prefix('/execucao')->group(function () {
        Route::prefix('/campanha')->group(function () {
            require __DIR__ . '/../../Execucao/Campanha/Routes/CampanhaRoutes.php';
        });
        Route::prefix('/registro')->group(function () {
            require __DIR__ . '/../../Execucao/Registro/Routes/RegistroRoutes.php';
        });
    });
});
