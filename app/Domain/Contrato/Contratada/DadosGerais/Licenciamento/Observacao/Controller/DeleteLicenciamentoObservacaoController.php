<?php

namespace App\Domain\Contrato\Contratada\DadosGerais\Licenciamento\Observacao\Controller;

use App\Domain\Contrato\Contratada\DadosGerais\Licenciamento\Observacao\Services\LicenciamentoObservacaoService;
use App\Models\ContratoLicencaObservacao;
use App\Shared\Http\Controllers\Controller;

class DeleteLicenciamentoObservacaoController extends Controller
{
  public function __construct(private readonly LicenciamentoObservacaoService $licenciamentoObservacaoService)
  {
  }

  public function index(ContratoLicencaObservacao $observacao)
  {
    $contrato_id = $observacao->contrato_id;

    try {
      $this->licenciamentoObservacaoService->delete($observacao);

      return to_route('contratos.contratada.dados_gerais.index', ['contrato' => $contrato_id])->with('message', ['type' => 'success', 'content' => 'Registro atualizado!']);
    } catch (\Exception $e) {
      return to_route('contratos.contratada.dados_gerais.index', ['contrato' => $contrato_id])->with('message', ['type' => 'error', 'content' => $e->getMessage()]);
    }
  }
}
