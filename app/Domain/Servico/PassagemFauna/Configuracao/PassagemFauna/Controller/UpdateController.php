<?php

namespace App\Domain\Servico\PassagemFauna\Configuracao\PassagemFauna\Controller;

use App\Domain\Servico\PassagemFauna\Configuracao\PassagemFauna\Requests\UpdateRequest;
use App\Domain\Servico\PassagemFauna\Configuracao\PassagemFauna\Services\PassagemFaunaService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;

class UpdateController extends Controller
{
    public function __construct(private readonly PassagemFaunaService $passagemFaunaService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, UpdateRequest $request)
    {
        $response = $this->passagemFaunaService->update($request->validated());

        return to_route('contratos.contratada.servicos.passagem_fauna.configuracao.passagem_fauna.index', [
            'contrato' => $contrato->id,
            'servico' => $servico->id
        ])->with('message', $response['request']);
    }
}
