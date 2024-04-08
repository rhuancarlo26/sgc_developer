<?php

namespace App\Domain\Contrato\Contratada\Servico\Condicionante\Controller;

use App\Domain\Contrato\Contratada\Servico\Condicionante\Services\ServicoLicencaCondicionanteService;
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
      'servico_id' => $request->servico_id,
      'licenca_id' => $request->licenca['id'],
      'condicionante_id' => $request->condicionante['id']
    ];

    $response = $this->servicoLicencaCondicionanteService->StoreServicoLicencaCondicionte($post);

    return to_route('contratos.contratada.servicos.create', ['contrato' => $request->contrato_id, 'servico' => $request->servico_id])->with('message', $response['request']);
  }
}
