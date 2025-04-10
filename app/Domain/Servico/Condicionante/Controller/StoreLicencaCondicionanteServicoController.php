<?php

namespace App\Domain\Servico\Condicionante\Controller;

use App\Domain\Servico\Condicionante\Services\ServicoLicencaCondicionanteService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreLicencaCondicionanteServicoController extends Controller
{
  public function __construct(private readonly ServicoLicencaCondicionanteService $servicoLicencaCondicionanteService)
  {
  }

  public function index(Request $request)
  {
    $post = [
      'id_servico' => $request->servico_id,
      'id_licenca' => $request->licenca['id'],
      'id_condicionante' => $request->condicionante['id']
    ];

    $response = $this->servicoLicencaCondicionanteService->storeServicoLicencaCondicionte($post);

    return to_route('contratos.contratada.servicos.create', ['contrato' => $request->contrato_id, 'servico' => $request->servico_id])->with('message', $response['request']);
  }
}
