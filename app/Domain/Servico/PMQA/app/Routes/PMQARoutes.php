<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Servico\PMQA\app\Controller\EnviaFiscalController;
use App\Domain\Servico\PMQA\app\Controller\AprovacaoConfigController;

Route::prefix('/pmqa')->group(function () {
    Route::prefix('/configuracao')->group(function () {
        Route::post('{contrato}/{servico}/envia-fiscal-pmqa', [EnviaFiscalController::class, 'index'])
            ->name('contratos.contratada.servicos.pmqa.configuracao.envia-fiscal-pmqa');
        Route::get('{servico}/aprovacao-config-pmqa', [AprovacaoConfigController::class, 'index'])->name('aprovacao-config-pmqa.get');
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
