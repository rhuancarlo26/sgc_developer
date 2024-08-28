<?php

namespace App\Domain\Servico\ContOcorrencia\Relatorio\Controller;

use App\Domain\Servico\ContOcorrencia\Relatorio\Requests\StoreRequest;
use App\Domain\Servico\ContOcorrencia\Relatorio\Services\RelatorioService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class StoreController extends Controller
{
    public function __construct(private readonly RelatorioService $relatorioService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, StoreRequest $request): RedirectResponse
    {
        $post = [
            ...$request->validated(),
            'id_servico' => $servico->id,
            'fk_status' => 1
        ];
        
        $response = $this->relatorioService->store($post);

        return to_route('contratos.contratada.servicos.cont_ocorrencia.relatorio.index', ['contrato' => $contrato->id, 'servico' => $servico->id])->with('message', $response['request']);
    }
}
