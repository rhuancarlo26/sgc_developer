<?php

namespace App\Domain\Contrato\Contratada\app\Controller;

use App\Domain\Contrato\Contratada\app\Services\DadosGeraisService;
use App\Models\Contrato;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DadosGeraisContratadaController extends Controller
{
  public function __construct(private readonly DadosGeraisService $dadosGeraisService)
  {
  }

  public function index(Contrato $contrato): Response
  {
    $response = $this->dadosGeraisService->index($contrato);

    return Inertia::render('Contrato/Contratada/DadosGerais/Index', $response);
  }
}
