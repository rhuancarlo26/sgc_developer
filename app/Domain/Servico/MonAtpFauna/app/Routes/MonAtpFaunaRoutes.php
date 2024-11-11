<?php

use App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Controller\DownloadModeloController;
use Illuminate\Support\Facades\Route;

Route::prefix('/mon_atp_fauna')->group(function () {
    Route::prefix('/configuracoes')->group(function () {
        Route::prefix('/vincular_abio')->group(function () {
            require __DIR__ . '../../../Configuracoes/VincualarABIO/app/Routes/VincularABIORoutes.php';
        });
        Route::prefix('/malha_viaria')->group(function () {
            require __DIR__ . '../../../Configuracoes/MalhaViaria/app/Routes/MalhaViariaRoutes.php';
        });
    });
    Route::prefix('/execucao')->group(function () {
        Route::prefix('/campanhas')->group(function () {
            require __DIR__ . '../../../Execucao/Campanhas/app/Routes/CampanhasRoutes.php';
        });

        Route::get('modelo_importar', [DownloadModeloController::class, 'index'])->name('contratos.contratada.servicos.mon_atp_fauna.execucao.registros.modelo_importar');

        Route::prefix('/registros')->group(function () {
            require __DIR__ . '../../../Execucao/Registros/app/Routes/RegistrosRoutes.php';
        });
    });
    Route::prefix('/resultado')->group(function () {
        require __DIR__ . '../../../Resultado/app/Routes/ResultadoRoutes.php';
    });
    Route::prefix('/relatorios')->group(function () {
        require __DIR__ . '../../../Relatorios/app/Routes/RelatoriosRoutes.php';
    });
    Route::prefix('/pareceres')->group(function () {
        require __DIR__ . '../../../Pareceres/app/Routes/PareceresRoutes.php';
    });
});
