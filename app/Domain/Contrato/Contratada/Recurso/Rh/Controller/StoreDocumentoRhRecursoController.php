<?php

namespace App\Domain\Contrato\Contratada\Recurso\Rh\Controller;

use App\Domain\Contrato\Contratada\Recurso\Equipamento\Services\EquipamentoRecursoService;
use App\Domain\Contrato\Contratada\Recurso\Rh\Services\RhRecursoService;
use App\Domain\Contrato\Contratada\Recurso\Veiculo\Services\VeiculoRecursoService;
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

    return to_route('contratos.contratada.recurso.rh.create', ['contrato' => $request->contrato_id, 'rh' => $request->recurso_rh_id])->with('message', $response);
  }
}
