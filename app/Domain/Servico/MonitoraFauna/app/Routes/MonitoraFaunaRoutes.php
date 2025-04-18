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
  Route::prefix('/execucao')->group(function () {
    Route::prefix('/campanha')->group(function () {
      require __DIR__ . '../../../Execucao/Campanha/Routes/CampanhaRoutes.php';
    });
    Route::prefix('/registro')->group(function () {
      require __DIR__ . '../../../Execucao/Registro/Routes/RegistroRoutes.php';
    });
  });
  Route::prefix('/resultado')->group(function () {
    require __DIR__ . '../../../Resultado/Routes/ResultadoRoutes.php';
  });
});
