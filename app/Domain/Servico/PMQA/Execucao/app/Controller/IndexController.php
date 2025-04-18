<?php

namespace App\Domain\Servico\PMQA\Execucao\app\Controller;

use App\Domain\Servico\PMQA\Execucao\app\Services\CampanhaService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __construct(private readonly CampanhaService $campanhaService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, Request $request): Response
    {
        $searchParams = $request->all('columns', 'value');

        $response = $this->campanhaService->index($servico, $searchParams);

        return Inertia::render('Servico/PMQA/Execucao/Index', [
            'contrato' => $contrato,
            'servico' => $servico->load(['tipo', 'pmqa_config_lista_parecer']),
            ...$response
        ]);
    }
}
