<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Controller;

use App\Domain\Servico\Condicionante\Services\ServicoLicencaCondicionanteService;
use App\Domain\Servico\MonAtpFauna\Configuracoes\VincualarABIO\app\Services\VincularABIOService;
use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Services\CampanhasService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __construct(
        private readonly CampanhasService $campanhasService,
        private readonly VincularABIOService $abioService,
        private readonly ServicoLicencaCondicionanteService $servicoLicencaCondicionanteService,
    ) {}

    public function index(Contrato $contrato, Servicos $servico, Request $request): Response
    {
        $searchParams = $request->all('columns', 'value');

        return Inertia::render('Servico/MonAtpFauna/Execucao/Campanhas/Index', [
            'contrato' => $contrato,
            'servico' => $servico->load(['tipo', 'servico_rhs.rh', 'servico_equipamentos.equipamento', 'servico_veiculos.veiculo', 'parecer_atropelamento']),
            'data' => $this->campanhasService->index($servico, $searchParams),
            'licencasVigente' => $this->servicoLicencaCondicionanteService->getLicencaMalhaViariaVigente($servico->id),
            'configVinculacao' => $this->abioService->getVinculos($servico->id),
        ]);
    }
}
