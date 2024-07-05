<?php

namespace App\Domain\Servico\PMQA\Execucao\Medir\app\Controller;

use App\Domain\Servico\PMQA\Execucao\Medir\app\Requests\StoreArquivoRequest;
use App\Domain\Servico\PMQA\Execucao\Medir\app\Services\MedicaoService;
use App\Models\Contrato;
use App\Models\ServicoPmqaCampanha;
use App\Models\ServicoPmqaCampanhaPonto;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class StoreArquivoController extends Controller
{
  public function __construct(private readonly MedicaoService $medicaoService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico, ServicoPmqaCampanha $campanha, ServicoPmqaCampanhaPonto $ponto, StoreArquivoRequest $request): RedirectResponse
  {
    $response = $this->medicaoService->storeArquivo($request->validated());

    return to_route('contratos.contratada.servicos.pmqa.execucao.medir.create', ['contrato' => $contrato->id, 'servico' => $servico->id, 'campanha' => $campanha->id, 'ponto' => $ponto->id])->with('message', $response['request']);
  }
}