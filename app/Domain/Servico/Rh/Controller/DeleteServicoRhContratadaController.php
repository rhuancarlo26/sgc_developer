<?php

namespace App\Domain\Servico\Rh\Controller;

use App\Domain\Servico\Rh\Services\ServicoRhService;
use App\Models\RecursoRh;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeleteServicoRhContratadaController extends Controller
{
  public function __construct(private readonly ServicoRhService $servicoRhService)
  {
  }

  public function index(RecursoRh $rh, Request $request)
  {
    $response = $this->servicoRhService->deleteRh($rh, $request->all());

    return to_route('contratos.contratada.servicos.create', ['contrato' => $request->contrato_id, 'servico' => $request['servico_id']])->with('message', $response['request']);
  }
}
