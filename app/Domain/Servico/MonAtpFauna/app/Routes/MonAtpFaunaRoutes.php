<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/mon_atp_fauna')->group(function () {
  Route::prefix('/configuracao')->group(function () {
    Route::prefix('/vincular_abio')->group(function () {
      require __DIR__ . '../../../Configuracao/VincualarABIO/app/Routes/VincularABIORoutes.php';
    });
  });
});