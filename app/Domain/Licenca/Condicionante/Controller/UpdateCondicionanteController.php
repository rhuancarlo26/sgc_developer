<?php

namespace App\Domain\Licenca\Condicionante\Controller;

use App\Domain\Licenca\Condicionante\Request\UpdateCondicionanteRequest;
use App\Domain\Licenca\Condicionante\Services\CondicionanteService;
use App\Models\LicencaCondicionante;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class UpdateCondicionanteController extends Controller
{
  public function __construct(private readonly CondicionanteService $condicionanteService)
  {
  }

  public function update(LicencaCondicionante $condicionante, UpdateCondicionanteRequest $request): RedirectResponse
  {
    $response = $this->condicionanteService->update($condicionante->id, $request->all());

    return to_route('licenca.condicionante.index', ['licenca' => $condicionante->licenca_id])->with('message', $response);
  }
}