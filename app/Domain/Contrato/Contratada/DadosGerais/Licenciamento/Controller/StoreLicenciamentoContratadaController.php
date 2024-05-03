<?php

namespace App\Domain\Contrato\Contratada\DadosGerais\Licenciamento\Controller;

use App\Domain\Contrato\Contratada\DadosGerais\Licenciamento\Services\LicenciamentoService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreLicenciamentoContratadaController extends Controller
{
  public function __construct(private readonly LicenciamentoService $licenciamentoService)
  {
  }

  public function index(Request $request)
  {
    $form = [
      'contrato_id' => $request->contrato_id,
      'licenca_id' => $request->licenca['id']
    ];

    $response = $this->licenciamentoService->salvarLicenciamento($form);

    return to_route('contratos.contratada.dados_gerais.index', ['contrato' => $request->contrato_id])->with('message', $response['request']);
  }
}
