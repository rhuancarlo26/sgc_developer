<?php

namespace App\Domain\Servico\ControleDeOcorrencias\Execucao\ControleRNC\Controller;

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


    $rncs = [
      (object) [
        'id' => 6,
        'nome_id' => "RNC.01.230-PA",
        'intensidade' => "Leve",
        'data_ocorrenciaF' => "22/03/2023",
        'data_fimF' => "24/11/2023",
        'ocorrencia_anterior' => "RNC Direto",
        'dias_restantes' => "Indeterminado",
        'nome_lote' => "L.01",
        'empresa' => "ECOPLAN ENGENHARIA LTDA.",
        'status' => "Aprovado",
        'status_atendimento' => "Atendida",
        'envio_empresa' => "Sim",
      ],
      (object) [
        'id' => 10,
        'nome_id' => "RNC.02.230-PA",
        'intensidade' => "Moderada",
        'data_ocorrenciaF' => "01/07/2023",
        'data_fimF' => "09/10/2023",
        'ocorrencia_anterior' => "RNC Direto",
        'dias_restantes' => "15",
        'nome_lote' => "L.01",
        'empresa' => "ECOPLAN ENGENHARIA LTDA.",
        'status' => "Aprovado",
        'status_atendimento' => "Atendida",
        'envio_empresa' => "Sim",
      ],
      (object) [
        'id' => 12,
        'nome_id' => "RNC.03.230-PA",
        'intensidade' => "Grave",
        'data_ocorrenciaF' => "09/07/2023",
        'data_fimF' => "16/11/2023",
        'ocorrencia_anterior' => "ROA.13.230-PA",
        'dias_restantes' => "90",
        'nome_lote' => "L.01",
        'empresa' => "ECOPLAN ENGENHARIA LTDA.",
        'status' => "Aprovado",
        'status_atendimento' => "Atendida",
        'envio_empresa' => "Sim",
      ],
      (object) [
        'id' => 15,
        'nome_id' => "RNC.04.230-PA",
        'intensidade' => "Leve",
        'data_ocorrenciaF' => "09/08/2023",
        'data_fimF' => "22/11/2023",
        'ocorrencia_anterior' => "ROA.14.230-PA",
        'dias_restantes' => "Indeterminado",
        'nome_lote' => "L.01",
        'empresa' => "ECOPLAN ENGENHARIA LTDA.",
        'status' => "Aprovado",
        'status_atendimento' => "Atendida",
        'envio_empresa' => "Sim",
      ],
      (object) [
        'id' => 16,
        'nome_id' => "RNC.05.230-PA",
        'intensidade' => "Leve",
        'data_ocorrenciaF' => "05/09/2023",
        'data_fimF' => "16/11/2023",
        'ocorrencia_anterior' => "ROA.15.230-PA",
        'dias_restantes' => "60",
        'nome_lote' => "L.01",
        'empresa' => "ECOPLAN ENGENHARIA LTDA.",
        'status' => "Reprovado",
        'status_atendimento' => "Atendida",
        'envio_empresa' => "Não",
      ],
      (object) [
        'id' => 17,
        'nome_id' => "RNC.06.230-PA",
        'intensidade' => "Grave",
        'data_ocorrenciaF' => "01/11/2023",
        'data_fimF' => "23/11/2023",
        'ocorrencia_anterior' => "RNC Direto",
        'dias_restantes' => "15",
        'nome_lote' => "L.01",
        'empresa' => "ECOPLAN ENGENHARIA LTDA.",
        'status' => "Aprovado",
        'status_atendimento' => "Atendida",
        'envio_empresa' => "Sim",
      ],
    ];


    return Inertia::render('Servico/ControleDeOcorrencias/Execucao/ControleRNC/Index', [
      'contrato'  => $contrato,
      'servico'   => $servico->load(['tipo']),
      'rncs'   => $rncs,
      ...$response
    ]);
  }
}
