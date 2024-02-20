<?php

namespace App\Domain\Licenca\Condicionante\Controller;

use App\Domain\Licenca\Condicionante\Services\CondicionanteService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreImportacaoCondicionanteController extends Controller
{
  public function __construct(private readonly CondicionanteService $condicionanteService)
  {
  }

  public function storeImportacao(Request $request)
  {
    $this->condicionanteService->storeImportacao($request->all());

    return to_route('licenca.condicionante.index', ['licenca' => $request->licenca['id']])->with('message', ['type' => 'success', 'content' => 'Condicionante cadastrado com sucesso!']);
  }
}