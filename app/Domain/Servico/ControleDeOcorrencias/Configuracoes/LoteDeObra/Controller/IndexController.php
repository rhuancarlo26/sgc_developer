<?php

namespace App\Domain\Servico\ControleDeOcorrencias\Configuracoes\LoteDeObra\Controller;

use App\Domain\Servico\ControleDeOcorrencias\Configuracoes\LoteDeObra\Services\LoteDeObraService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __construct(private readonly LoteDeObraService $loteDeObraService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, Request $request): Response
    {

        $searchParams = $request->all('columns', 'value');

        $response = $this->loteDeObraService->index($servico, $searchParams);

        $listas = [
            (object) [
                'nome_id' => "1",
                'nome' => "Lote A",
                'rodovia' => "230",
                'uf' => "PA",
                'km_inicial' => "0",
                'km_final' => "984",
                'empresa' => "Construtora X",
                'num_contrato' => "1336/2020",
                'situacao_contrato' => "Em andamento",
                'supervisora_obras' => "Ana Silva",
            ],
            (object) [
                'nome_id' => "2",
                'nome' => "Lote B",
                'rodovia' => "381",
                'uf' => "MG",
                'km_inicial' => "0",
                'km_final' => "500",
                'empresa' => "Construtora Y",
                'num_contrato' => "1200/2019",
                'situacao_contrato' => "Concluído",
                'supervisora_obras' => "Carlos Souza",
            ],
            (object) [
                'nome_id' => "3",
                'nome' => "Lote C",
                'rodovia' => "116",
                'uf' => "SP",
                'km_inicial' => "0",
                'km_final' => "450",
                'empresa' => "Construtora Z",
                'num_contrato' => "1100/2018",
                'situacao_contrato' => "Em andamento",
                'supervisora_obras' => "Mariana Oliveira",
            ],
            (object) [
                'nome_id' => "4",
                'nome' => "Lote D",
                'rodovia' => "101",
                'uf' => "RS",
                'km_inicial' => "0",
                'km_final' => "300",
                'empresa' => "Construtora W",
                'num_contrato' => "1400/2021",
                'situacao_contrato' => "Pendente",
                'supervisora_obras' => "Roberto Lima",
            ]
        ];
        
        return Inertia::render('Servico/ControleDeOcorrencias/Configuracoes/LoteDeObra/Index', [
            'contrato'  => $contrato,
            'servico'   => $servico->load(['tipo']),
            'listas'    => $listas,
            ...$response
        ]);
    }
}
