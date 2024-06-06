<?php

namespace App\Domain\Sgc\Contratada\app\Controller;

use App\Domain\Sgc\Contratada\app\Services\DadosGeraisService;
use App\Models\Contrato;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DadosGeraisSgcController extends Controller
{
  public function __construct(private readonly DadosGeraisService $dadosGeraisService)
  {
  }

  public function index(Contrato $contrato): Response
  {
    $response = $this->dadosGeraisService->index($contrato);

    return Inertia::render('Sgc/Contratada/DadosGerais/Index', $response);
  }
}
