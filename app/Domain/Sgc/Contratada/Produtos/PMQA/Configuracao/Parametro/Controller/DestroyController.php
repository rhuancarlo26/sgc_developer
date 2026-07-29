<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Parametro\Controller;

use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Parametro\Services\ParametroService;
use App\Models\Contrato;
use App\Models\SgcPmqa;
use App\Models\SgcPmqaParametroLista;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DestroyController extends Controller
{
  public function __construct(private readonly ParametroService $parametroService)
  {
  }

  public function index(Contrato $contrato, SgcPmqa $pmqa, SgcPmqaParametroLista $lista): RedirectResponse
  {
    $response = $this->parametroService->destroy($lista);

    return to_route('contratos.contratada.servicos.pmqa.configuracao.parametro.index', ['contrato' => $contrato->id, 'pmqa' => $pmqa->id])->with('message', $response['request']);
  }
}
