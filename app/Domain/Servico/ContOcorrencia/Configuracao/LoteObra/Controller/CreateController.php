<?php

namespace App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Controller;

use App\Domain\Servico\ContOcorrencia\Configuracao\Empreendimento\Services\EmpreendimentoService;
use App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Services\LoteObraService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CreateController extends Controller
{
  public function __construct(private readonly LoteObraService $loteObraService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico): Response
  {
    // $response = $this->empreendimentoService->index($servico, $searchParams);

    return Inertia::render('Servico/ContOcorr/Configuracao/LoteObra/Form', [
      'contrato' => $contrato,
      'servico' => $servico->load(['tipo', 'pmqa_config_lista_parecer']),
      // ...$response
    ]);
  }
}