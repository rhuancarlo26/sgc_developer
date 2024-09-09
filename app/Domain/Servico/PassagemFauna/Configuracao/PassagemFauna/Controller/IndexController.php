<?php

namespace App\Domain\Servico\PassagemFauna\Configuracao\PassagemFauna\Controller;

use App\Domain\Servico\PassagemFauna\Configuracao\PassagemFauna\Services\PassagemFaunaService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IndexController extends Controller
{
    public function __construct(private readonly PassagemFaunaService $passagemFaunaService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, Request $request)
    {
        $searchParams = $request->all('columns', 'value');

        $response = $this->passagemFaunaService->index($servico, $searchParams);

        return Inertia::render('Servico/PassagemFauna/Configuracao/PassagemFauna/Index', [
            'contrato' => $contrato,
            'servico' => $servico->load(['tipo']),
            ...$response
        ]);
    }
}
