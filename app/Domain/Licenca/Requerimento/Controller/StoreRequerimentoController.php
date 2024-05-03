<?php

namespace App\Domain\Licenca\Requerimento\Controller;

use App\Domain\Licenca\Requerimento\Services\RequerimentoService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreRequerimentoController extends Controller
{
  public function __construct(private readonly RequerimentoService $requerimentoService)
  {
  }

  public function index(Request $request): RedirectResponse
  {
    $response = $this->requerimentoService->store($request->arquivos, $request->licenca_id);

    return to_route('licenca.index')->with('message', $response);
  }
}
