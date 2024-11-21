<?php

namespace App\Domain\Contrato\Contratada\DadosGerais\Anexo\Controller;

use App\Models\ContratoAnexo;
use App\Shared\Http\Controllers\Controller;

class VisualizarAnexoContratadaController extends Controller
{
  public function index(ContratoAnexo $anexo)
  {
    return response()->file(storage_path('app') . DIRECTORY_SEPARATOR . $anexo->caminho_arquivo);
  }
}
