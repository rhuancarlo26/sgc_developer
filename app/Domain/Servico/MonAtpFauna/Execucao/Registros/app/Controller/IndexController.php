<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Controller;

use App\Domain\Servico\Condicionante\Services\ServicoLicencaCondicionanteService;
use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Services\CampanhasService;
use App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Services\RegistrosService;
use App\Domain\Servico\PMQA\Configuracao\Parametro\Services\ParametroService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Models\Uf;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __construct(
        private readonly RegistrosService $registrosService,
        private readonly CampanhasService $campanhasService,
        private readonly ServicoLicencaCondicionanteService $servicoLicencaCondicionanteService
    ) {}

    public function index(Contrato $contrato, Servicos $servico, Request $request): Response
    {
        $searchParams = $request->all('columns', 'value');

        return Inertia::render('Servico/MonAtpFauna/Execucao/Registros/Index', [
            'contrato' => $contrato,
            'servico' => $servico->load(['tipo', 'parecer_atropelamento']),
            'ufs' => Cache::rememberForever('ufs', fn() => Uf::all()),
            'data' => $this->registrosService->index($servico, $searchParams),
            'campanhas' => $this->campanhasService->getCampanhas(servicoId: $servico->id, searchParams: ['', ''], paginate: false),
            'licencasVigente' => $this->servicoLicencaCondicionanteService->getLicencaMalhaViariaVigente($servico->id),
        ]);
    }
}