<?php

namespace App\Domain\Servico\PMQA\Configuracao\VinculacaoPonto\Controller;

use App\Domain\Servico\PMQA\Configuracao\VinculacaoPonto\Services\VinculacaoPontoService;
use App\Models\Contrato;
use App\Models\ServicoPmqaListaParametro;
use App\Models\ServicoPmqaParametroLista;
use App\Models\ServicoPmqaPonto;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteController extends Controller
{
  public function __construct(private readonly VinculacaoPontoService $vinculacaoPontoService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico, ServicoPmqaParametroLista $lista): RedirectResponse
  {
    $response = $this->vinculacaoPontoService->deleteVinculacao($lista);

    return to_route('contratos.contratada.servicos.pmqa.configuracao.vinculacao_ponto.index', ['contrato' => $contrato->id, 'servico' => $servico->id])->with('message', $response['request']);
  }
}