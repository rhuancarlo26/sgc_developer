<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/pmqa')->group(function () {
  require __DIR__ . "../../../PMQA/Routes/PMQARoutes.php";
});

Route::prefix('/cont_ocorrencia')->group(function () {
  require __DIR__ . "../../../ContOcorrencia/Routes/ContOcorrenciaRoutes.php";
});

Route::prefix('/supressao_vegetacao')->group(function () {
  require __DIR__ . "../../../SupressaoVegetacao/Routes/SupressaoRoutes.php";
});
