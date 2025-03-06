<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Requests\StoreRequest;
use App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Service\ResultadoService;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;

class CreateResultadoController extends Controller
{
    public function __construct(private readonly ResultadoService $resultadoService)
    {
    }

    public function index(StoreRequest $request, Servicos $servico)
    {
        $this->resultadoService->create($request->validated(), $servico);
        return redirect()->back()->with('message',  [
            'type' => 'success',
            'content' =>  'Resultado criado com sucesso'
        ]);
    }
}