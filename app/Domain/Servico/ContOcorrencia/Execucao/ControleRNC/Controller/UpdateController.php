<?php

namespace App\Domain\Servico\ContOcorrencia\Execucao\ControleRNC\Controller;

use App\Domain\Servico\ContOcorrencia\Execucao\ControleRNC\Requests\UpdateRequest;
use App\Domain\Servico\ContOcorrencia\Execucao\ControleRNC\Services\ControleRNCService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class UpdateController extends Controller
{
    public function __construct(private readonly ControleRNCService $controleRNCService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, UpdateRequest $request): RedirectResponse
    {
        $response = $this->controleRNCService->update(post: $request->validated());

        return to_route('contratos.contratada.servicos.cont_ocorrencia.execucao.controle_rnc.index', ['contrato' => $contrato->id, 'servico' => $servico->id])->with('message', $response['request']);
    }
}
