<?php

namespace App\Domain\Servico\PassagemFauna\Configuracao\VincularABIO\Controller;

use App\Domain\Servico\PassagemFauna\Configuracao\VincularABIO\Services\VincularABIOService;
use App\Models\Contrato;
use App\Models\ServicoPassagemFaunaConfigAbioRet;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;

class DeleteRETController extends Controller
{
    public function __construct(private readonly VincularABIOService $vincularABIO)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoPassagemFaunaConfigAbioRet $abioRet)
    {
        $response = $this->vincularABIO->destroyRet($abioRet);

        return to_route('contratos.contratada.servicos.passagem_fauna.configuracao.vincular_abio.index', [
            'contrato' => $contrato->id,
            'servico' => $servico->id
        ])->with('message', $response['request']);
    }
}
