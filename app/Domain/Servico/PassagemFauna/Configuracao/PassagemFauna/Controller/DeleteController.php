<?php

namespace App\Domain\Servico\PassagemFauna\Configuracao\PassagemFauna\Controller;

use App\Domain\Servico\PassagemFauna\Configuracao\PassagemFauna\Services\PassagemFaunaService;
use App\Models\Contrato;
use App\Models\ServicoPassagemFaunaConfigPassagem;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;

class DeleteController extends Controller
{
    public function __construct(private readonly PassagemFaunaService $passagemFaunaService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoPassagemFaunaConfigPassagem $passagem_fauna)
    {
        $response = $this->passagemFaunaService->destroy($passagem_fauna);

        return to_route('contratos.contratada.servicos.passagem_fauna.configuracao.passagem_fauna.index', [
            'contrato' => $contrato->id,
            'servico' => $servico->id
        ])->with('message', $response['request']);
    }
}
