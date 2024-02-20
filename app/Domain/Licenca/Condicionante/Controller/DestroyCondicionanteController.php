<?php

namespace App\Domain\Licenca\Condicionante\Controller;

use App\Domain\Licenca\Condicionante\Services\CondicionanteService;
use App\Models\LicencaCondicionante;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DestroyCondicionanteController extends Controller
{
  public function __construct(private readonly CondicionanteService $condicionanteService)
  {
  }

  public function destroy(LicencaCondicionante $condicionante): RedirectResponse
  {
    try {
      $this->condicionanteService->delete($condicionante);

      $response = [
        'type' => 'success',
        'content' => 'Condicionante deletado com sucesso!'
      ];
    } catch (\Exception $th) {
      $response = [
        'type' => 'error',
        'content' => $th->getMessage()
      ];
    }

    return to_route('licenca.condicionante.index', ['licenca' => $condicionante->licenca_id])->with('message', $response);
  }
}