<?php

namespace App\Domain\Fiscal\Parecer\Controllers;

use App\Domain\Fiscal\Parecer\Services\ParecerService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;


class EmiteParecerConfigPassagemFaunaController extends Controller
{
  public function __construct(private readonly ParecerService $parecerService) {}

  public function index(Request $request)
  {
    $response = $this->parecerService->emiteParecerConfigPassagemFauna($request->all());

    return to_route(route: 'fiscal.configuracoes.passagem_fauna.index', parameters: ['contrato' => $request->id_contrato])
      ->with('message', $response['request']);
  }
}
