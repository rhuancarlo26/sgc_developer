<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Controller;


use App\Domain\Servico\SupressaoVegetacao\Configuracao\VincularASV\Services\VincularASVService;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Services\EstagioSucessionalService;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Services\TipoBiomaService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{

    public function __construct(
        private readonly EstagioSucessionalService $estagioSucessionalService,
        private readonly TipoBiomaService $tipoBiomaService,
        private readonly VincularASVService $vincularASVService,
    )
    {
    }

    public function __invoke(Contrato $contrato, Servicos $servico, Request $request): Response
    {
        return Inertia::render(component: 'Servico/SupressaoVegetacao/Execucao/Supressao/Index', props: [
            'contrato' => $contrato,
            'servico' => $servico->load(['tipo']),
            'estagios' => $this->estagioSucessionalService->all(columns: ['id', 'nome']),
            'biomas' => $this->tipoBiomaService->all(columns: ['id', 'nome']),
            'licencas' => $this->vincularASVService->search('servico_id', $servico->id)
                ->with(['licenca.tipo'])
                ->get(),
        ]);
    }

}
