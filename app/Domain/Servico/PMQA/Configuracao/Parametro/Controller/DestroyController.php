<?php

namespace App\Domain\Servico\PMQA\Configuracao\Parametro\Controller;

use App\Domain\Servico\PMQA\Configuracao\Parametro\Services\ParametroService;
use App\Models\Contrato;
use App\Models\ServicoPmqaParametroLista;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DestroyController extends Controller
{
  public function __construct(private readonly ParametroService $parametroService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico, ServicoPmqaParametroLista $lista): RedirectResponse
  {
    $response = $this->parametroService->destroy($lista);

    return to_route('contratos.contratada.servicos.pmqa.configuracao.parametro.index', ['contrato' => $contrato->id, 'servico' => $servico->id])->with('message', $response['request']);
  }
}