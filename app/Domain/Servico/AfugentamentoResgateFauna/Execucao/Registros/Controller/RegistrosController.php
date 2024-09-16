<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Service\RegistrosService;
use App\Shared\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Models\Servicos;
use Inertia\Inertia;
use Inertia\Response;

class RegistrosController extends Controller
{
    public function __construct(private readonly RegistrosService $registrosService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico): Response
    {
        $grupoAmostrado = $this->registrosService->getGrupoAmostrado();
        $frenteSupressao = $this->registrosService->getFrenteSupressao($servico);
        $formaRegistro = $this->registrosService->getFormaRegistro();
        $registros = $this->registrosService->getRegistros($servico);
        $ufs = $this->registrosService->getUfs();
        $statusConservacaoFederal = $this->registrosService->getStatusConservacaoFederal();
        $statusConservacaoIucn = $this->registrosService->getStatusConservacaoIucn();
        return Inertia::render('Servico/AfugentamentoResgateFauna/Execucao/Registros', [
            'contrato'  => $contrato,
            'servico'   => $servico->load(['tipo']),
            'grupoAmostrado' => $grupoAmostrado,
            'frenteSupressao' => $frenteSupressao,
            'formaRegistro' => $formaRegistro,
            'registros' => $registros,
            'ufs' => $ufs,
            'statusConservacaoFederal' => $statusConservacaoFederal,
            'statusConservacaoIucn' => $statusConservacaoIucn,
        ]);
    }
}