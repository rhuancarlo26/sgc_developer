<?php

namespace App\Domain\Contrato\Contratada\Recurso\Rh\Controller;

use App\Domain\Contrato\Contratada\Recurso\Rh\Services\RhRecursoService;
use App\Models\RecursoRhDocumentoBaixa;
use App\Shared\Http\Controllers\Controller;

class VisualizarDocumentoBaixaRhRecursoController extends Controller
{
  public function __construct(private readonly RhRecursoService $rhRecursoService)
  {
  }

  public function index(RecursoRhDocumentoBaixa $documento_baixa)
  {
    return response()->file(storage_path('app') . DIRECTORY_SEPARATOR . $documento_baixa->caminho);
  }
}