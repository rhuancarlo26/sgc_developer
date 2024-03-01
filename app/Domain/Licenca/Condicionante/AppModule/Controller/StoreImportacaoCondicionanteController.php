<?php

namespace App\Domain\Licenca\Condicionante\AppModule\Controller;

use App\Domain\Licenca\Condicionante\AppModule\Services\CondicionanteService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreImportacaoCondicionanteController extends Controller
{
  public function __construct(private readonly CondicionanteService $condicionanteService)
  {
  }

  public function index(Request $request): RedirectResponse
  {
    $response = $this->condicionanteService->storeImportacao($request->all());

    return to_route(route: 'licenca.condicionante.index', parameters: $response['licenca'])->with('message', $response['request']);
  }
}