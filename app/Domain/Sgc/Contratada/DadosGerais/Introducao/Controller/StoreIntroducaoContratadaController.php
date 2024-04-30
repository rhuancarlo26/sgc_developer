<?php

namespace App\Domain\Contrato\Contratada\DadosGerais\Introducao\Controller;

use App\Domain\Contrato\Contratada\DadosGerais\Introducao\Services\IntroducaoService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreIntroducaoContratadaController extends Controller
{
  public function __construct(private readonly IntroducaoService $introducaoService)
  {
  }

  public function index(Request $request)
  {
    $response = $this->introducaoService->salvarIntroducao($request->all());

    return to_route('contratos.contratada.dados_gerais.index', ['contrato' => $request->contrato_id])->with('message', $response['request']);
  }
}
