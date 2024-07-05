<?php

namespace App\Domain\Servico\PMQA\Execucao\Controller;

use App\Domain\Servico\PMQA\Execucao\Services\ExecucaoService;
use App\Models\Contrato;
use App\Models\ServicoPmqaCampanha;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DestroyController extends Controller
{
  public function __construct(private readonly ExecucaoService $execucaoService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico, ServicoPmqaCampanha $campanha): RedirectResponse
  {
    $response = $this->execucaoService->destroy($campanha);

    return to_route('contratos.contratada.servicos.pmqa.execucao.index', ['contrato' => $contrato->id, 'servico' => $servico->id])->with('message', $response['request']);
  }
}