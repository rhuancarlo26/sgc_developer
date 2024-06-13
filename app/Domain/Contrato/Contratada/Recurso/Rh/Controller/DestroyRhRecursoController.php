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
    $response = $this->rhRecursoService->destroyRh($rh);

    return back()->with('message', $response);

  }
}
