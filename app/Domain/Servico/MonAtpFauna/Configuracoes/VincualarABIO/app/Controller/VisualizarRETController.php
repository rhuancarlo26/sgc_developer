<?php

namespace App\Domain\Servico\MonAtpFauna\Configuracoes\VincualarABIO\app\Controller;

use App\Shared\Http\Controllers\Controller;

class VisualizarRETController extends Controller
{
  public function index($ret)
  {
    $filePath = storage_path($ret->caminho_arquivo);

    if (!file_exists($filePath)) {
      return response()->json(['message' => 'Arquivo não encontrado'], 404);
    }

    return response()->file($filePath, [
      'Content-Disposition' => 'inline; filename="' . $ret->nome_arquivo . '"'
    ]);
  }
}
