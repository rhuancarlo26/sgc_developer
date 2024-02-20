<?php

namespace App\Domain\Licenca\Condicionante\Controller;

use App\Domain\Licenca\Condicionante\Request\StoreCondicionanteRequest;
use App\Domain\Licenca\Condicionante\Services\CondicionanteService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class StoreCondicionanteController extends Controller
{
  public function __construct(private readonly CondicionanteService $condicionanteService)
  {
  }

  public function store(StoreCondicionanteRequest $request): RedirectResponse
  {
    $response = $this->condicionanteService->store($request->except(['id']));

    return to_route('licenca.condicionante.index', ['licenca' => $request->licenca_id])->with('message', $response);
  }
}