<?php

namespace App\Domain\Servico\PassagemFauna\Configuracao\VincularABIO\Controller;

use App\Domain\Servico\PassagemFauna\Configuracao\VincularABIO\Services\VincularABIOService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __construct(private readonly VincularABIOService $vincularABIO)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, Request $request): Response
    {
        $searchParams = $request->all('columns', 'value');

        $response = $this->vincularABIO->index($servico, $searchParams);

        return Inertia::render('Servico/PassagemFauna/Configuracao/VincularABIO/Index', [
            'contrato' => $contrato,
            'servico' => $servico->load(['tipo', 'parecer_passagem_fauna']),
            ...$response
        ]);
    }
}
