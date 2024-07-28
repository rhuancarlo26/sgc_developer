<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/supressao-vegetacao')->group(function () {
  Route::prefix('/configuracao')->group(function () {
      require __DIR__ . '/../../Configuracao/VincularASV/Routes/VincularASVRoutes.php';
  });
});
