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

Route::prefix('/mont_atp_fauna')->group(function () {
  require __DIR__ . "../../../MonAtpFauna/Routes/AtropelamentoRoutes.php";
});

Route::prefix('/afugentamento')->group(function () {
  require __DIR__ . "../../../AfugentamentoResgateFauna/Routes/afugentamentoRoutes.php";
});
