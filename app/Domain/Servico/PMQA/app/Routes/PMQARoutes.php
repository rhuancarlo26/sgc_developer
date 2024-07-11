<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/pmqa')->group(function () {
  Route::prefix('/configuracao')->group(function () {
    require __DIR__ . '/../../Configuracao/Ponto/Routes/PontoRoutes.php';
    require __DIR__ . '/../../Configuracao/Parametro/Routes/ParametroRoutes.php';
    require __DIR__ . '/../../Configuracao/VinculacaoPonto/Routes/VinculacaoPontoRoutes.php';
  });

  require __DIR__ . '/../../Execucao/app/Routes/ExecucaoRoutes.php';

  require __DIR__ . '/../../Resultado/app/Routes/ResultadoRoutes.php';

  Route::prefix('/relatorio')->group(function () {
    require __DIR__ . '/../../Relatorio/app/Routes/RelatorioRoutes.php';
  });
});