<?php

namespace App\Domain\Fiscal\Relatorio\PMQA\Controller;

use App\Domain\Fiscal\Relatorio\PMQA\Services\PMQAService;
use App\Models\Contrato;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreController extends Controller
{
  public function __construct(private readonly PMQAService $pmqaService) {}

  public function index(Contrato $contrato, Request $request): RedirectResponse
  {
    $response = $this->pmqaService->store($request->all());

    return to_route(route: 'fiscal.relatorio.pmqa.index', parameters: ['contrato' => $contrato->id])
      ->with('message', $response['request']);
  }
}
