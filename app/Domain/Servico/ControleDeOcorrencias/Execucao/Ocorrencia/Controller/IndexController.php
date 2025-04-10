<?php

namespace App\Domain\Servico\ControleDeOcorrencias\Execucao\Ocorrencia\Controller;

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

        $ocorrencias = [
            (object) [
                'nome_id' => "ROA.12.230-PA",
                'intensidade' => "Leve",
                'data_ocorrenciaF' => "03/05/2023",
                'data_fimF' => "15/05/2023",
                'ocorrencia_anterior' => "",
                'dias_restantes' => "",
                'nome_lote' => "L.01",
                'empresa' => "ECOPLAN ENGENHARIA LTDA.",
                'status' => "Atendida",
                'envio_empresa' => "Sim"
            ],
            (object) [
                'nome_id' => "ROA.20.230-PA",
                'intensidade' => "Grave",
                'data_ocorrenciaF' => "08/11/2023",
                'data_fimF' => "",
                'ocorrencia_anterior' => "",
                'dias_restantes' => "Vencido",
                'nome_lote' => "L.02",
                'empresa' => "CONSÓRCIO ARTELESTE/DELTACON",
                'status' => "Em Aberto",
                'envio_empresa' => "Sim"
            ],
            (object) [
                'nome_id' => "ROA.22.230-PA",
                'intensidade' => "Grave",
                'data_ocorrenciaF' => "01/06/2024",
                'data_fimF' => "",
                'ocorrencia_anterior' => "",
                'dias_restantes' => "1",
                'nome_lote' => "L.02",
                'empresa' => "CONSÓRCIO ARTELESTE/DELTACON",
                'status' => "Em Aberto",
                'envio_empresa' => "Sim"
            ],

        ];

        return Inertia::render('Servico/ControleDeOcorrencias/Execucao/Ocorrencia/Index', [
            'contrato'      => $contrato,
            'servico'       => $servico->load(['tipo']),
            'ocorrencias'   => $ocorrencias,
            ...$response
        ]);
    }
}
