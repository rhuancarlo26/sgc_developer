<?php

namespace App\Domain\Servico\PassagemFauna\Configuracao\VincularABIO\Controller;

use App\Domain\Servico\PassagemFauna\Configuracao\VincularABIO\Services\VincularABIOService;
use App\Models\Contrato;
use App\Models\ServicoPassagemFaunaConfigAbio;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;

class DeleteABIOController extends Controller
{
    public function __construct(private readonly VincularABIOService $vincularABIOService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoPassagemFaunaConfigAbio $abio)
    {
        $response = $this->vincularABIOService->destroyABIO($abio->id);

        return to_route('contratos.contratada.servicos.passagem_fauna.configuracao.vincular_abio.index', [
            'contrato' => $contrato->id,
            'servico' => $servico->id
        ])->with('message', $response['request']);
    }
}
