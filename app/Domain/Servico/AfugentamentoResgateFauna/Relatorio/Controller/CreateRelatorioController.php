<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Relatorio\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\Relatorio\Requests\StoreRequest;
use App\Domain\Servico\AfugentamentoResgateFauna\Relatorio\Service\RelatorioService;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;


class CreateRelatorioController extends Controller
{
    public function __construct(private readonly RelatorioService $relatorioService)
    {
    }

    public function index(StoreRequest $request, Servicos $servico)
    {
        $this->relatorioService->create($request->validated(), $servico);
        return redirect()->back()->with('message',  [
            'type' => 'success',
            'content' =>  'Relatório criado com sucesso'
        ]);
    }
}