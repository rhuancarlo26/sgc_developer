<?php

namespace App\Domain\Servico\PassagemFauna\Execucao\Registro\Controller;

use App\Domain\Servico\PassagemFauna\Execucao\Registro\Services\RegistroService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreArquivoController extends Controller
{
    public function __construct(private readonly RegistroService $registroService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, Request $request)
    {
        $response = $this->registroService->storeArquivo($request->all());

        return to_route('contratos.contratada.servicos.passagem_fauna.execucao.registro.create', [
            'contrato' => $contrato->id,
            'servico' => $servico->id,
            'registro' => $request->id
        ])->with('message', $response['request']);
    }
}
