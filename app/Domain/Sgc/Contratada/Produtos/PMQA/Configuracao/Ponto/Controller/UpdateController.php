<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Controller;

use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Requests\UpdateRequest;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Services\PontoService;
use App\Models\Contrato;
use App\Models\SgcPmqa;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class UpdateController extends Controller
{
  public function __construct(private readonly PontoService $pontoService)
  {
  }

  public function index(Contrato $contrato, SgcPmqa $campanha, UpdateRequest $updateRequest): RedirectResponse
  {

    $data = $updateRequest->validated();
    $response = $this->pontoService->updateParaCampanha($data);

    return redirect()->back()->with('message', $response['request'] ?? $response);
  }
}
