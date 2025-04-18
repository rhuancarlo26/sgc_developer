<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Servico\AfugentamentoResgateFauna\app\Controller\AprovacaoConfigController;
use App\Domain\Servico\AfugentamentoResgateFauna\app\Controller\EnviaFiscalController;

Route::prefix('afugentamento-resgate-fauna')->group(function () {
    Route::prefix('/configuracao')->group(function () {

        Route::post('{contrato}/{servico}/envia-fiscal', [EnviaFiscalController::class, 'index'])
            ->name('contratos.contratada.servicos.afugentamento.configuracao.envia-fiscal');

        Route::get('{servico}/aprovacao-config-afugentamento', [AprovacaoConfigController::class, 'index'])
            ->name('aprovacao-config-afugentamento.get');

        require __DIR__ . '/../../Configuracao/VincularASV/Routes/VincularASVRoutes.php';
        require __DIR__ . '/../../Configuracao/VincularABIO/Routes/VincularABIORoutes.php';
    });

    Route::prefix('/execucao')->group(function () {
        require __DIR__ . '/../../Execucao/FrenteSupressao/Routes/FrenteSupressaoRoutes.php';
        require __DIR__ . '/../../Execucao/Registros/Routes/RegistrosRoutes.php';
    });

    Route::prefix('/resultado')->group(function () {
        require __DIR__ . '/../../Resultado/Routes/ResultadoRoutes.php';
        require __DIR__ . '/../../Resultado/Routes/Analise/AnaliseRoutes.php';
        require __DIR__ . '/../../Resultado/Routes/OutraAnalise/OutraAnaliseRoutes.php';
    });

    Route::prefix('/relatorio')->group(function () {
        require __DIR__ . '/../../Relatorio/Routes/RelatorioRoutes.php';
    });

    Route::prefix('/pareceres')->group(function () {
        require __DIR__ . '/../../Pareceres/Routes/PareceresRoutes.php';
    });
});
