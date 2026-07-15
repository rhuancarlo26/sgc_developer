<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\VinculacaoPonto\Controller;

use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\VinculacaoPonto\Services\VinculacaoPontoService;
use App\Models\Contrato;
use App\Models\ServicoPmqaListaParametro;
use App\Models\ServicoPmqaParametroLista;
use App\Models\ServicoPmqaPonto;
use App\Models\Servicos;
use App\Models\SgcPmqa;
use App\Models\SgcPmqaParametroLista;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteController extends Controller
{
  public function __construct(private readonly VinculacaoPontoService $vinculacaoPontoService)
  {
  }

  public function index(Contrato $contrato, SgcPmqa $pmqa, SgcPmqaParametroLista $lista): RedirectResponse
  {
    $response = $this->vinculacaoPontoService->deleteVinculacao($lista);

    return to_route('contratos.contratada.servicos.pmqa.configuracao.vinculacao_ponto.index', ['contrato' => $contrato->id, 'servico' => $pmqa->id])->with('message', $response['request']);
  }
}
