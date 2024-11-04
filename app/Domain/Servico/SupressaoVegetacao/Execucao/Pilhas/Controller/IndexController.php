<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Controller;

use App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Services\PilhasService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{

    public function __construct(private readonly PilhasService $pilhasService,) {}

    public function __invoke(Contrato $contrato, Servicos $servico, Request $request): Response
    {
        $searchParams = $request->all('columns', 'value');

        $response = $this->pilhasService->index(servico: $servico, searchParams: $searchParams);

        return Inertia::render(component: 'Servico/SupressaoVegetacao/Execucao/Pilhas/Index', props: [
            'contrato' => $contrato,
            'servico' => $servico->load(['tipo']),
            ...$response
        ]);
    }
}
