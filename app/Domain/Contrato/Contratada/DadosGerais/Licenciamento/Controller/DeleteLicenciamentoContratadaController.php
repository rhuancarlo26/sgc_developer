<?php

namespace App\Domain\Contrato\Contratada\DadosGerais\Licenciamento\Controller;

use App\Domain\Contrato\Contratada\DadosGerais\Licenciamento\Services\LicenciamentoService;
use App\Models\Licenca;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeleteLicenciamentoContratadaController extends Controller
{
  public function __construct(private readonly LicenciamentoService $licenciamentoService)
  {
  }

  public function index(Licenca $licenca, Request $request)
  {
    $response = $this->licenciamentoService->deleteLicenciamento($licenca, $request->all());

    return to_route('contratos.contratada.dados_gerais.index', ['contrato' => $request->contrato_id])->with('message', $response);
  }
}
