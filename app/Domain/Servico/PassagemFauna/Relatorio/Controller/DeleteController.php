<?php

namespace App\Domain\Servico\PassagemFauna\Relatorio\Controller;

use App\Domain\Servico\PassagemFauna\Relatorio\Services\RelatorioService;
use App\Models\Contrato;
use App\Models\ServicoPassagemFaunaRelatorio;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;

class DeleteController extends Controller
{
    public function __construct(private readonly RelatorioService $relatorioService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoPassagemFaunaRelatorio $relatorio)
    {
        $response = $this->relatorioService->destroy($relatorio);

        return to_route('contratos.contratada.servicos.passagem_fauna.relatorio.index', [
            'contrato' => $contrato->id,
            'servico' => $servico->id
        ])->with('message', $response['request']);
    }
}
