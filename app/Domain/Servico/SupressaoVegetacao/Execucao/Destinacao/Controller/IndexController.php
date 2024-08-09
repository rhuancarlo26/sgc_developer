<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Destinacao\Controller;


use App\Domain\Servico\SupressaoVegetacao\Configuracao\PatioEstocagem\Services\PatioEstocagemService;
use App\Domain\Servico\SupressaoVegetacao\Configuracao\VincularASV\Services\VincularASVService;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Destinacao\Services\DestinacaoService;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Enums\TipoPilha;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Services\PilhasService;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Services\TipoProdutoService;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Services\SupressaoService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{

    public function __construct(
        private readonly VincularASVService    $vincularASVService,
        private readonly SupressaoService      $supressaoService,
        private readonly PatioEstocagemService $patioEstocagemService,
        private readonly TipoProdutoService    $tipoProdutoService,
        private readonly PilhasService         $pilhasService,
        private readonly DestinacaoService     $destinacaoService,
    )
    {
    }

    public function __invoke(Contrato $contrato, Servicos $servico, Request $request): Response
    {
        $searchParams = $request->all('columns', 'value');
        return Inertia::render(component: 'Servico/SupressaoVegetacao/Execucao/Destinacao/Index', props: [
            'contrato' => $contrato,
            'servico' => $servico->load(['tipo']),
            'data' => $this->destinacaoService->index(servico: $servico, searchParams: $searchParams),
            'pilhas' => $this->pilhasService->search('servico_id', $servico->id)->with(['corteEspecie', 'licenca'])->get(),

            'tipos' => TipoPilha::toArray(),
            'patios' => $this->patioEstocagemService->search('servico_id', $servico->id)->get(['id', 'chave']),
            'produtos' => $this->tipoProdutoService->all(columns: ['id', 'nome']),
            'areasSuprimidas' => $this->supressaoService->search('servico_id', $servico->id)->get(['id', 'chave']),
            'licencas' => $this->vincularASVService->search('servico_id', $servico->id)->with('licenca')->get(),
        ]);
    }

}
