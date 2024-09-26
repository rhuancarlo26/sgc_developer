<?php

namespace App\Domain\Servico\PassagemFauna\Resultado\Controller;

use App\Domain\Servico\PassagemFauna\Resultado\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __construct(private readonly ResultadoService $resultadoService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, Request $request): Response
    {
        $searchParams = $request->all('columns', 'value');

        $response = $this->resultadoService->index($servico, $searchParams);

        return Inertia::render('Servico/PassagemFauna/Resultado/Index', [
            'contrato' => $contrato,
            'servico' => $servico->load(['tipo']),
            ...$response
        ]);
    }
}
