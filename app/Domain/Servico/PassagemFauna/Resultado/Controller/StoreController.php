<?php

namespace App\Domain\Servico\PassagemFauna\Resultado\Controller;

use App\Domain\Servico\PassagemFauna\Resultado\Requests\StoreRequest;
use App\Domain\Servico\PassagemFauna\Resultado\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;

class StoreController extends Controller
{
    public function __construct(private readonly ResultadoService $resultadoService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, StoreRequest $request)
    {
        $response = $this->resultadoService->store(post: $request->validated());

        return to_route('contratos.contratada.servicos.passagem_fauna.resultado.index', [
            'contrato' => $contrato->id,
            'servico' => $servico->id
        ])->with('message', $response['request']);
    }
}
