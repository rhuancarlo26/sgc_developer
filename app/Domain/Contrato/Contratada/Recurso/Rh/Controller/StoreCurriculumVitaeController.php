<?php

namespace App\Domain\Contrato\Contratada\Recurso\Rh\Controller;

use App\Domain\Contrato\Contratada\Recurso\Rh\Services\RhRecursoService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreCurriculumVitaeController extends Controller
{
  public function __construct(private readonly RhRecursoService $rhRecursoService) {}

  public function index(Request $request)
  {
    $response = $this->rhRecursoService->salvarCurriculum($request->all());

    return to_route(route: 'contratos.contratada.recurso.rh.create', parameters: ['contrato' => $request->id_contrato, 'rh' => $request->id])
      ->with('message', $response['request']);
  }
}
