<?php

namespace App\Domain\Contrato\Contratada\Recurso\Rh\Controller;

use App\Domain\Contrato\Contratada\Recurso\Rh\Services\RhRecursoService;
use App\Models\RecursoRh;
use App\Shared\Http\Controllers\Controller;

class DestroyRhRecursoController extends Controller
{
  public function __construct(private readonly RhRecursoService $rhRecursoService)
  {
  }

  public function index(RecursoRh $rh)
  {
    try {
      $response = $this->rhRecursoService->destroyRh($rh);

      $this->rhRecursoService->delete($rh);

      return to_route('contratos.contratada.recurso.rh.index', ['contrato' => $rh->contrato_id])->with('message', $response);
    } catch (\Exception $e) {
      return to_route('contratos.contratada.recurso.rh.index', ['contrato' => $rh->contrato_id])->with('message', $response);
    }
  }
}
