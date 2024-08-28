<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/controledeocorrencias')->group(function () {
  // Configurações
  Route::prefix('/configuracoes')->group(function () {
    require __DIR__ . '../../../Configuracoes/Empreendimento/Routes/EmpreendimentoRoutes.php';
    require __DIR__ . '../../../Configuracoes/LoteDeObra/Routes/LoteDeObraRoutes.php';
  });

  // Execução
  Route::prefix('/execucao')->group(function () {
    require __DIR__ . '../../../Execucao/Ocorrencia/Routes/OcorrenciaRoutes.php';
    require __DIR__ . '../../../Execucao/ControleRNC/Routes/ControleRNCRoutes.php';
    require __DIR__ . '../../../Execucao/ACA/Routes/ACARoutes.php';
  });
  
  // Resultado
  Route::prefix('/resultado')->group(function () {
    require __DIR__ . '../../../Resultado/Routes/ResultadoRoutes.php';
  });
  
  // Relatorios
  Route::prefix('/relatorios')->group(function () {
    require __DIR__ . '../../../Relatorios/Routes/RelatoriosRoutes.php';
  });
  
  // Pareceres
  Route::prefix('/pareceres')->group(function () {
    require __DIR__ . '../../../Pareceres/Routes/PareceresRoutes.php';
  });
});