<?php

namespace App\Domain\Contrato\Contratada\Licenciamento\Observacao\Controller;

use App\Domain\Contrato\Contratada\Licenciamento\Observacao\Services\LicenciamentoObservacaoService;
use App\Domain\Contrato\Contratada\Licenciamento\Services\LicenciamentoService;
use App\Models\ContratoLicenca;
use App\Models\ContratoLicencaObservacao;
use App\Shared\Http\Controllers\Controller;

class DeleteLicenciamentoObservacaoController extends Controller
{
  public function __construct(private readonly LicenciamentoObservacaoService $licenciamentoObservacaoService)
  {
  }

  public function index(ContratoLicencaObservacao $observacao)
  {
    try {
      $this->licenciamentoObservacaoService->delete($observacao);

      $response = [
        'type' => 'success',
        'content' => 'Licenciamento deletado com sucesso!'
      ];
    } catch (\Exception $th) {
      $response = [
        'type' => 'error',
        'content' => $th->getMessage()
      ];
    }

    return to_route('contratos.contratada.dados_gerais.index', ['contrato' => $observacao->contrato_id])->with('message', $response);
  }
}