<?php

namespace App\Domain\Servico\MonAtpFauna\Pareceres\app\Controller;

use App\Domain\Servico\MonAtpFauna\Pareceres\app\Services\PareceresService;
use App\Domain\Servico\PMQA\Configuracao\Parametro\Services\ParametroService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __construct(private readonly PareceresService $pareceresService) {}

    public function index(Contrato $contrato, Servicos $servico, Request $request): Response
    {
        $searchParams = $request->all('columns', 'value');

        return Inertia::render('Servico/MonAtpFauna/Pareceres/Index', [
            'contrato' => $contrato,
            'servico' => $servico->load(['tipo', 'parecer_atropelamento']),
            'data' => $this->pareceresService->index($servico, $searchParams)
        ]);
    }
}