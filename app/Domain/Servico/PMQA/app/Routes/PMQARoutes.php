<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/pmqa')->group(function () {
  Route::prefix('/configuracao')->group(function () {
    // Ponto
    require __DIR__ . '/../../Configuracao/Ponto/Routes/PontoRoutes.php';
  });
});
