<?php

namespace App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Controller;

use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Services\OcorrenciaService;
use App\Models\Contrato;
use App\Models\ServicoConOcorrOcorrenciSupervisaoExecOcorrencia;
use App\Models\ServicoConOcorrSupervisaoExecOcorrenciaVistoria;
use App\Models\ServicoConOcorrSupervisaoExecOcorrenciaVistoriaArquivoPrazo;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteVistoriaArquivoController extends Controller
{
    public function __construct(private readonly OcorrenciaService $ocorrenciaService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoConOcorrOcorrenciSupervisaoExecOcorrencia $ocorrencia, ServicoConOcorrSupervisaoExecOcorrenciaVistoria $vistoria, ServicoConOcorrSupervisaoExecOcorrenciaVistoriaArquivoPrazo $arquivo): RedirectResponse
    {
        $response = $this->ocorrenciaService->destroyVistoriaArquivo($arquivo);

        return to_route('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.create_vistoria', ['contrato' => $contrato->id, 'servico' => $servico->id, 'ocorrencia' => $vistoria->id_ocorrencia])->with('message', $response['request']);
    }
}
