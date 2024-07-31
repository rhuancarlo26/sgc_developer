<?php

namespace App\Domain\Servico\ControleDeOcorrencias\Pareceres\Controller;

use App\Domain\Servico\ControleDeOcorrencias\Configuracoes\Empreendimento\Services\EmpreendimentoService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
  public function __construct(private readonly EmpreendimentoService $empreendimentoService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico, Request $request): Response
  {

    $searchParams = $request->all('columns', 'value');
    
    $response = $this->empreendimentoService->index($servico, $searchParams);
    
    return Inertia::render('Servico/ControleDeOcorrencias/Pareceres/Index', [
      'contrato'  => $contrato,
      'servico'   => $servico->load(['tipo']),
      ...$response
    ]);
  }
}