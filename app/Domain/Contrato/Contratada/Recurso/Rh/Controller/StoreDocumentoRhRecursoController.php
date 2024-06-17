<?php

namespace App\Domain\Contrato\Contratada\Recurso\Rh\Controller;

use App\Domain\Contrato\Contratada\Recurso\Rh\Services\RhRecursoService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreDocumentoRhRecursoController extends Controller
{
  public function __construct(private readonly RhRecursoService $rhRecursoService)
  {
  }

  public function index(Request $request)
  {
    $response = $this->rhRecursoService->salvarDocumentoRh($request->all());

    return back()->withInput([
      'contrato' => $request->contrato_id,
      'rh' => $response['rh']
    ])->with('message', $response['request']);
  }
}
