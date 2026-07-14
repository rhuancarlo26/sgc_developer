<?php

namespace App\Domain\Fiscal\Parecer\Controllers;

namespace App\Domain\Fiscal\Parecer\Controllers;

use App\Domain\Fiscal\Parecer\Services\ParecerService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;


class EmiteParecerConfigAtropelamentoController extends Controller
{
  public function __construct(private readonly ParecerService $parecerService) {}

  public function index(Request $request)
  {
    $response = $this->parecerService->emiteParecerConfigAtropelamentoFauna($request->all());

    return to_route(route: 'fiscal.configuracoes.atropelamento.index', parameters: ['contrato' => $request->id_contrato])
      ->with('message', $response['request']);
  }
}
