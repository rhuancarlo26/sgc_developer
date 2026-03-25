<?php

namespace App\Domain\Contrato\Contratada\Recurso\Rh\Controller;

use App\Domain\Contrato\Contratada\Recurso\Rh\Services\RhRecursoService;
use App\Models\Contrato;
use App\Models\RecursoRhDocumento;
use App\Shared\Http\Controllers\Controller;

class DestroyDocumentoRhRecursoController extends Controller
{
  public function __construct(private readonly RhRecursoService $rhRecursoService)
  {
  }

  public function index(Contrato $contrato, RecursoRhDocumento $model_documento)
  {
    $response = $this->rhRecursoService->destroyDocumento($model_documento);

    return back()->withInput([
      'contrato' => $contrato->id,
      'rh' => $model_documento->recurso_rh_id
    ])->with('message', $response);
  }
}
