<?php

namespace App\Domain\Contrato\Contratada\Licenciamento\Controller;

use App\Domain\Contrato\Contratada\Licenciamento\Services\LicenciamentoService;
use App\Models\ContratoLicenca;
use App\Shared\Http\Controllers\Controller;

class DeleteLicenciamentoContratadaController extends Controller
{
  public function __construct(private readonly LicenciamentoService $licenciamentoService)
  {
  }

  public function index(ContratoLicenca $licenciamento)
  {
    dd($licenciamento);
    try {
      $this->licenciamentoService->delete($licenciamento);

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

    return to_route('contratos.contratada.dados_gerais.index', ['contrato' => $licenciamento->contrato_id])->with('message', $response);
  }
}