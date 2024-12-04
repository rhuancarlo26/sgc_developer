<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/monitora_fauna')->group(function () {
  Route::prefix('/configuracoes')->group(function () {
    Route::prefix('/vincular_abio')->group(function () {
      require __DIR__ . '../../../Configuracao/VincularABIO/Routes/VincularABIORoutes.php';
    });
    Route::prefix('/modulo_amostral')->group(function () {
      require __DIR__ . '../../../Configuracao/ModuloAmostral/Routes/ModuloAmostralRoutes.php';
    });
  });
});
