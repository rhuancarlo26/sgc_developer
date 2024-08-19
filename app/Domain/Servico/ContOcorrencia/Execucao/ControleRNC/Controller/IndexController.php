<?php

namespace App\Domain\Servico\ContOcorrencia\Execucao\ControleRNC\Controller;

use App\Domain\Servico\ContOcorrencia\Execucao\ControleRNC\Services\ControleRNCService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __construct(private readonly ControleRNCService $controleRNCService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, Request $request): Response
    {
        $searchParams = $request->all('columns', 'value');

        $response = $this->controleRNCService->index($servico->id, $searchParams);

        return Inertia::render('Servico/ContOcorr/Execucao/ControleRNC/Index', [
            'contrato' => $contrato,
            'servico' => $servico->load(['tipo']),
            ...$response
        ]);
    }
}
