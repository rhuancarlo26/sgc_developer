<?php

namespace App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Controller;

use App\Domain\Servico\ContOcorrencia\Configuracao\Empreendimento\Services\EmpreendimentoService;
use App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Services\LoteObraService;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Requests\StoreRequest;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Requests\UpdateRequest;
use App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Services\OcorrenciaService;
use App\Models\Contrato;
use App\Models\ServicoConOcorrOcorrenciSupervisaoExecOcorrencia;
use App\Models\ServicoConOcorrSupervicaoExecOcorrenciaRegistro;
use App\Models\Servicos;
use App\Models\Uf;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class VisualizarRegistroController extends Controller
{
    public function index(Contrato $contrato, Servicos $servico, ServicoConOcorrSupervicaoExecOcorrenciaRegistro $registro): BinaryFileResponse
    {
        $caminhoRelativo = ltrim(preg_replace('/^public[\/\\\\]/', '', $registro->caminho_arquivo), '/\\');

        $caminho = storage_path('app/public/' . $caminhoRelativo);

        if (!file_exists($caminho)) {
            abort(404, 'Arquivo não encontrado.');
        }

        return response()->file($caminho);
    }
}
