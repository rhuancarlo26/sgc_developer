<?php

namespace App\Domain\Contrato\Contratada\DadosGerais\Licenciamento\Observacao\Controller;

use App\Domain\Contrato\Contratada\DadosGerais\Licenciamento\Observacao\Services\LicenciamentoObservacaoService;
use App\Domain\Licenca\Requests\UpdateLicencaRequest;
use App\Domain\Licenca\Services\LicencaService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdateLicenciamentoObservacaoController extends Controller
{
  public function __construct(private readonly LicenciamentoObservacaoService $licenciamentoObservacaoService)
  {
  }

  public function index(Request $request): \Illuminate\Http\RedirectResponse
  {
    $response = $this->licenciamentoObservacaoService->update(post: $request->all());

    return to_route('contratos.contratada.dados_gerais.index', ['contrato' => $request->contrato_id])->with('message', $response['request']);
  }
}
