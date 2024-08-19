<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/cont_ocorrencia')->group(function () {
    Route::prefix('/configuracao')->group(function () {
        Route::prefix('/empreendimento')->group(function () {
            require __DIR__ . '/../../Configuracao/Empreendimento/Routes/EmpreendimentoRoutes.php';
        });
        Route::prefix('/lote_obra')->group(function () {
            require __DIR__ . '/../../Configuracao/LoteObra/Routes/LoteObraRoutes.php';
        });
    });
    Route::prefix('/execucao')->group(function () {
        Route::prefix('/ocorrencia')->group(function () {
            require __DIR__ . '/../../Execucao/Ocorrencia/Routes/OcorrenciaRoutes.php';
        });
        Route::prefix('/controle_rnc')->group(function () {
            require __DIR__ . '/../../Execucao/ControleRNC/Routes/ControleRNCRoutes.php';
        });
        Route::prefix('/aca')->group(function () {
            require __DIR__ . '/../../Execucao/ACA/Routes/ACARoutes.php';
        });
    });
});
