<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/supressao-vegetacao')->group(function () {
  Route::prefix('/configuracao')->group(function () {
      require __DIR__ . '/../../Configuracao/VincularASV/Routes/VincularASVRoutes.php';
      require __DIR__ . '/../../Configuracao/PlanoSupressao/Routes/PlanoSupressaoRoutes.php';
      require __DIR__ . '/../../Configuracao/PatioEstocagem/Routes/PatioEstocagemRoutes.php';
      require __DIR__ . '/../../Execucao/Supressao/Routes/SupressaoRoutes.php';
      require __DIR__ . '/../../Execucao/Pilhas/Routes/PilhasRoutes.php';
      require __DIR__ . '/../../Execucao/Destinacao/Routes/DestinacaoRoutes.php';
  });
});
