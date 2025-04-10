<?php

namespace App\Domain\Fiscal\app\Controllers;

use App\Domain\Fiscal\app\services\FiscalService;
use App\Models\Contrato;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ListagemConfiguracoesAtropelamentoFaunaController extends Controller
{
  public function __construct(private readonly FiscalService $servicoService) {}

  public function index(Contrato $contrato, Request $request): Response
  {
    $searchParams = $request->all('searchColumn', 'searchValue');

    $response = $this->servicoService->listagemConfiguracao(contrato: $contrato, searchParams: $searchParams, id_servico: 3);

    return Inertia::render('Fiscal/Configuracao/Atropelamento/Index', [
      'contrato' => $contrato,
      ...$response
    ]);
  }
}
