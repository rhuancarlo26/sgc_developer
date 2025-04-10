<?php

namespace App\Domain\Servico\ControleDeOcorrencias\Execucao\ACA\Controller;

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

    $acas = [
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

        'id_aca' => "ACA.01",
        'data_aca' => "09/10/2023",
        'relacao_rncs_aca' => "RNC.02.230-PA",
        'lote_aca' => "L.01",
        'construtora_aca' => "ECOPLAN ENGENHARIA LTDA.",
        'envio_aca' => "Sim",
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
        
        'id_aca' => "ACA.02",
        'data_aca' => "16/11/2023",
        'relacao_rncs_aca' => "RNC.03.230-PA, RNC.07.230-PA",
        'lote_aca' => "L.01",
        'construtora_aca' => "ECOPLAN ENGENHARIA LTDA.",
        'envio_aca' => "Sim",
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
        
        'id_aca' => "ACA.03",
        'data_aca' => "22/11/2023",
        'relacao_rncs_aca' => "RNC.04.230-PA",
        'lote_aca' => "L.01",
        'construtora_aca' => "ECOPLAN ENGENHARIA LTDA.",
        'envio_aca' => "Sim",
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
        
        'id_aca' => "ACA.04",
        'data_aca' => "23/11/2023",
        'relacao_rncs_aca' => "RNC.06.230-PA",
        'lote_aca' => "L.01",
        'construtora_aca' => "ECOPLAN ENGENHARIA LTDA.",
        'envio_aca' => "Sim",
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
        
        'id_aca' => "ACA.05",
        'data_aca' => "23/11/2023",
        'relacao_rncs_aca' => "RNC.09.230-PA",
        'lote_aca' => "L.02",
        'construtora_aca' => "CONSÓRCIO ARTELESTE/DELTACON",
        'envio_aca' => "Sim",
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
        
        'id_aca' => "ACA.06",
        'data_aca' => "24/11/2023",
        'relacao_rncs_aca' => "RNC.01.230-PA, RNC.08.230-PA",
        'lote_aca' => "L.01",
        'construtora_aca' => "ECOPLAN ENGENHARIA LTDA.",
        'envio_aca' => "Sim",
      ],
    ];
    
    return Inertia::render('Servico/ControleDeOcorrencias/Execucao/ACA/Index', [
      'contrato'  => $contrato,
      'servico'   => $servico->load(['tipo']),
      'acas'   => $acas,
      ...$response
    ]);
  }
}