<?php

namespace App\Domain\Servico\Condicionante\Controller;

use App\Domain\Servico\Condicionante\Services\ServicoLicencaCondicionanteService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeleteLicencaCondicionanteServicoController extends Controller
{
  public function __construct(private readonly ServicoLicencaCondicionanteService $servicoLicencaCondicionanteService)
  {
  }

  public function index(Request $request)
  {
    $response = $this->servicoLicencaCondicionanteService->deleteServicoLicencaCondicionte($request->all());

    return to_route('contratos.contratada.servicos.create', ['contrato' => $request->contrato_id, 'servico' => $request->servico_id])->with('message', $response['request']);
  }
}
