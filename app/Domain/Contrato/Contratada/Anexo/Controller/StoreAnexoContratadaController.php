<?php

namespace App\Domain\Contrato\Contratada\Anexo\Controller;

use App\Domain\Contrato\Contratada\Anexo\Services\AnexoService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreAnexoContratadaController extends Controller
{
  public function __construct(private readonly AnexoService $anexoService)
  {
  }

  public function index(Request $request)
  {
    $response = $this->anexoService->salvarAnexo($request->all());

    return to_route('contratos.contratada.dados_gerais.index', ['contrato' => $request->contrato_id])->with('message', $response['request']);
  }
}