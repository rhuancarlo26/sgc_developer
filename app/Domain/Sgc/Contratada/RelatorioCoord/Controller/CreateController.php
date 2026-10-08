<?php

namespace App\Domain\Sgc\Contratada\RelatorioCoord\Controller;

use App\Domain\Sgc\Contratada\RelatorioCoord\Services\CreateRelatorioService;
use App\Domain\Sgc\Contratada\RelatorioCoord\Requests\CreateRelatorioRequest;

class CreateController
{
    protected $createRelatorioService;

    public function __construct(CreateRelatorioService $createRelatorioService)
    {
        $this->createRelatorioService = $createRelatorioService;
    }

    public function index(CreateRelatorioRequest $request)
    {
        $dados = $request->validated();
        $this->createRelatorioService->iniciarNovoRelatorio(
            $dados['contrato'], $dados['data_inicio'], $dados['data_fim']
        );

        return back()->with('success', 'Relatório criado com sucesso!');
    }
}
