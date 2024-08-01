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
});