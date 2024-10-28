<?php

use App\Domain\Fiscal\Relatorio\ContOcorrencia\Controller\IndexController as ControllerIndexController;
use App\Domain\Fiscal\Relatorio\PMQA\Controller\IndexController;
use App\Domain\Fiscal\Relatorio\PMQA\Controller\StoreController;
use Illuminate\Support\Facades\Route;

Route::prefix('/pmqa')->group(function () {
  require __DIR__ . "../../../PMQA/Routes/PMQARoutes.php";
});

Route::prefix('/cont_ocorrencia')->group(function () {
  require __DIR__ . "../../../ContOcorrencia/Routes/ContOcorrenciaRoutes.php";
});
