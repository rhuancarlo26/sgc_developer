<?php

namespace App\Domain\Contrato\Contratada\Recurso\Rh\Controller;

use App\Domain\Contrato\Contratada\Recurso\Rh\Services\RhRecursoService;
use App\Models\RecursoRh;
use App\Models\RecursoRhDocumento;
use App\Shared\Http\Controllers\Controller;

class VisualizarCurriculumVitaeController extends Controller
{
  public function __construct(private readonly RhRecursoService $rhRecursoService) {}

  public function index(RecursoRh $rh)
  {
    return response()->file(storage_path('app') . DIRECTORY_SEPARATOR . $rh->curriculum_pdf);
  }
}
