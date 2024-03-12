<?php

namespace App\Domain\Contrato\Contratada\Licenciamento\Observacao\Controller;

use App\Domain\Contrato\Contratada\Licenciamento\Observacao\Services\LicenciamentoObservacaoService;
use App\Domain\Contrato\Contratada\Licenciamento\Services\LicenciamentoService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreLicenciamentoObservacaoController extends Controller
{
  public function __construct(private readonly LicenciamentoObservacaoService $licenciamentoObservacaoService)
  {
  }

  public function index(Request $request)
  {
    $response = $this->licenciamentoObservacaoService->salvarLicenciamentoObservacao($request->all());

    return to_route('contratos.contratada.dados_gerais.index', ['contrato' => $request->contrato_id])->with('message', $response['request']);
  }
}
