<?php

namespace App\Domain\Servico\PMQA\Configuracao\VinculacaoPonto\Controller;

use App\Domain\Servico\PMQA\Configuracao\VinculacaoPonto\Services\VinculacaoPontoService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class EnviarListaFiscalController extends Controller
{
  public function __construct(private readonly VinculacaoPontoService $vinculacaoPontoService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico): RedirectResponse
  {
    $post = [
      'servico_id' => $servico->id,
      'status_id' => 2
    ];

    $response = $this->vinculacaoPontoService->enviarListaFiscal($post);

    return to_route('contratos.contratada.servicos.pmqa.configuracao.vinculacao_ponto.index', ['contrato' => $contrato->id, 'servico' => $servico->id])->with('message', $response['request']);
  }
}
