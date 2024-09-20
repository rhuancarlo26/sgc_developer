<?php

namespace App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Controller;

use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Services\OcorrenciaService;
use App\Models\Contrato;
use App\Models\ServicoConOcorrOcorrenciSupervisaoExecOcorrencia;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class CreateController extends Controller
{
    public function __construct(private readonly OcorrenciaService $ocorrenciaService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoConOcorrOcorrenciSupervisaoExecOcorrencia $ocorrencia): Response
    {
        $post = [
            'servico_id' => $servico->id
        ];

        $response = $this->ocorrenciaService->create($post);

        return Inertia::render('Servico/ContOcorr/Execucao/Ocorrencia/Form', [
            'contrato' => $contrato,
            'servico' => $servico->load([
                'tipo',
                'cont_ocorr_parecer_configuracao',
                'licencas_condicionantes.licenca.segmentos.uf_inicial_rel',
                'licencas_condicionantes.licenca.segmentos.uf_final_rel'
            ]),
            'ocorrencia' => $ocorrencia->load(['lote', 'rodovia.uf', 'registros']),
            ...$response
        ]);
    }
}
