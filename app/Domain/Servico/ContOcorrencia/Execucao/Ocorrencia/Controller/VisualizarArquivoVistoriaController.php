<?php

namespace App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Controller;

use App\Models\Contrato;
use App\Models\ServicoConOcorrOcorrenciSupervisaoExecOcorrencia;
use App\Models\ServicoConOcorrSupervisaoExecOcorrenciaVistoria;
use App\Models\ServicoConOcorrSupervisaoExecOcorrenciaVistoriaArquivoPrazo;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;

class VisualizarArquivoVistoriaController extends Controller
{
    public function index(Contrato $contrato, Servicos $servico, ServicoConOcorrOcorrenciSupervisaoExecOcorrencia $ocorrencia, ServicoConOcorrSupervisaoExecOcorrenciaVistoria $vistoria, ServicoConOcorrSupervisaoExecOcorrenciaVistoriaArquivoPrazo $arquivo): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return response()->file(storage_path('app') . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $arquivo->caminho_arquivo);
    }
}
