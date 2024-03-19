<?php

namespace App\Domain\Contrato\Contratada\Controller;

use App\Domain\Contrato\Contratada\Services\DadosGeraisService;
use App\Models\Contrato;
use App\Models\Licenca;
use App\Models\Rodovia;
use App\Models\Uf;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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