<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Servico\SupressaoVegetacao\app\Controller\AprovacaoConfigController;
use App\Domain\Servico\SupressaoVegetacao\app\Controller\EnviaFiscalController;

Route::prefix('/supressao-vegetacao')->group(function () {
  Route::prefix('/configuracao')->group(function () {
      Route::post('{contrato}/{servico}/envia-fiscal-supressao', [EnviaFiscalController::class, 'index'])
          ->name('contratos.contratada.servicos.supressao.configuracao.envia-fiscal');
      Route::get('{servico}/aprovacao-config-supressao', [AprovacaoConfigController::class, 'index'])->name('aprovacao-config-supressao.get');
      require __DIR__ . '/../../Configuracao/VincularASV/Routes/VincularASVRoutes.php';
      require __DIR__ . '/../../Configuracao/PlanoSupressao/Routes/PlanoSupressaoRoutes.php';
      require __DIR__ . '/../../Configuracao/PatioEstocagem/Routes/PatioEstocagemRoutes.php';
      require __DIR__ . '/../../Execucao/Supressao/Routes/SupressaoRoutes.php';
      require __DIR__ . '/../../Execucao/Pilhas/Routes/PilhasRoutes.php';
  });
});
