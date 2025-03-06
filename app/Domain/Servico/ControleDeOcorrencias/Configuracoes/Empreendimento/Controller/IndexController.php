<?php

namespace App\Domain\Servico\ControleDeOcorrencias\Configuracoes\Empreendimento\Controller;

use App\Domain\Servico\ControleDeOcorrencias\Configuracoes\Empreendimento\Services\EmpreendimentoService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Faker\Factory as Faker;

class IndexController extends Controller
{
    public function __construct(private readonly EmpreendimentoService $empreendimentoService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, Request $request): Response
    {

        $searchParams = $request->all('columns', 'value');
        
        $response = $this->empreendimentoService->index($servico, $searchParams);
        
        // $listas = [
        //     (object) [
        //         'id' => "1",
        //         'uf_inicial' => "PA",
        //         'uf_final' => "PA",
        //         'rodovia' => "230",
        //         'empreendimento' => "BR-230/PA Div. TO/PA a Rurópolis",
        //         'km_inicio' => "0",
        //         'km_fim' => "984",
        //         'extensao_br' => "984",
        //         'numero_licenca' => "1336/2020"
        //     ],
        //     (object) [
        //         'id' => "2",
        //         'uf_inicial' => "MG",
        //         'uf_final' => "MG",
        //         'rodovia' => "381",
        //         'empreendimento' => "BR-381/MG Belo Horizonte a São Paulo",
        //         'km_inicio' => "0",
        //         'km_fim' => "500",
        //         'extensao_br' => "500",
        //         'numero_licenca' => "1200/2019"
        //     ],
        //     (object) [
        //         'id' => "3",
        //         'uf_inicial' => "SP",
        //         'uf_final' => "RJ",
        //         'rodovia' => "116",
        //         'empreendimento' => "BR-116/SP Rio de Janeiro a São Paulo",
        //         'km_inicio' => "0",
        //         'km_fim' => "450",
        //         'extensao_br' => "450",
        //         'numero_licenca' => "1100/2018"
        //     ],
        //     (object) [
        //         'id' => "4",
        //         'uf_inicial' => "RS",
        //         'uf_final' => "SC",
        //         'rodovia' => "101",
        //         'empreendimento' => "BR-101/RS Div. SC/RS a Porto Alegre",
        //         'km_inicio' => "0",
        //         'km_fim' => "300",
        //         'extensao_br' => "300",
        //         'numero_licenca' => "1400/2021"
        //     ]
        // ];

        return Inertia::render('Servico/ControleDeOcorrencias/Configuracoes/Empreendimento/Index', [
            'contrato'  => $contrato,
            'servico'   => $servico->load(['tipo']),
            // 'listas'    => $listas,
            ...$response
        ]);
    }
}
