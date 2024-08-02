<?php

namespace App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Controller;

use App\Domain\Servico\ContOcorrencia\Configuracao\Empreendimento\Services\EmpreendimentoService;
use App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Services\LoteObraService;
use App\Models\Contrato;
use App\Models\ServicoContOcorrSupervisaoConfigLote;
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

  public function index(Contrato $contrato, Servicos $servico, ServicoContOcorrSupervisaoConfigLote $lote): Response
  {
    $response = $this->loteObraService->create($servico);

    return Inertia::render('Servico/ContOcorr/Configuracao/LoteObra/Form', [
      'contrato' => $contrato,
      'servico' => $servico->load([
        'tipo',
        'pmqa_config_lista_parecer',
        'licencas_condicionantes.licenca.segmentos.uf_inicial',
        'licencas_condicionantes.licenca.segmentos.uf_final'
      ]),
      'lote' => $lote->load(['rodovia', 'uf']),
      ...$response
    ]);
  }
}
