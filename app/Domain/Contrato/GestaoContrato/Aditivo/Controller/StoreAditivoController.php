<?php

namespace App\Domain\Contrato\GestaoContrato\Aditivo\Controller;

use App\Domain\Contrato\GestaoContrato\Aditivo\Requests\StoreAditivoRequest;
use App\Domain\Contrato\GestaoContrato\Aditivo\Services\AditivoService;
use App\Shared\Http\Controllers\Controller;

class StoreAditivoController extends Controller
{
  public function __construct(private readonly AditivoService $aditivoService)
  {
  }

  public function index(StoreAditivoRequest $request)
  {
    $response = $this->aditivoService->store($request->validated());

    return to_route('contratos.gestao.create', ['tipo' => $request->tipo_id, 'contrato' => $request->contrato_id])->with('message', $response['request']);
  }
}
