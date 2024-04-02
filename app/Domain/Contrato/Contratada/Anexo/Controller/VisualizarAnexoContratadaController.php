<?php

namespace App\Domain\Contrato\Contratada\Anexo\Controller;

use App\Models\ContratoAnexo;
use App\Shared\Http\Controllers\Controller;
use App\Shared\Utils\ArquivoUtils;

class VisualizarAnexoContratadaController extends Controller
{
  public function index(ContratoAnexo $anexo)
  {
    return response()->file(storage_path('app') . DIRECTORY_SEPARATOR . $anexo->caminho);
  }
}