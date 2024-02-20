<?php

namespace App\Domain\Licenca\Condicionante\Controller;

use App\Domain\Licenca\Condicionante\Services\CondicionanteService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreImportacaoCondicionanteController extends Controller
{
  public function __construct(private readonly CondicionanteService $condicionanteService)
  {
  }

  public function storeImportacao(Request $request): RedirectResponse
  {
   $response = $this->condicionanteService->storeImportacao($request->all());

   dd($response);

    return to_route('licenca.condicionante.index', ['licenca' => $request->licenca['id']])->with('message', $response);
  }
}
