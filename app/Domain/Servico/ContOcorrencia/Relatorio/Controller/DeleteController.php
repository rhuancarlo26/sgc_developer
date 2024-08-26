<?php

namespace App\Domain\Servico\ContOcorrencia\Relatorio\Controller;

use App\Domain\Servico\ContOcorrencia\Relatorio\Services\RelatorioService;
use App\Models\Contrato;
use App\Models\ServicoConOcorrSupervisaoRelatorio;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteController extends Controller
{
    public function __construct(private readonly RelatorioService $relatorioService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoConOcorrSupervisaoRelatorio $relatorio): RedirectResponse
    {
        $response = $this->relatorioService->destroy(relatorio_id: $relatorio->id);

        return to_route('contratos.contratada.servicos.cont_ocorrencia.relatorio.index', ['contrato' => $contrato->id, 'servico' => $servico->id])->with('message', $response['request']);
    }
}
