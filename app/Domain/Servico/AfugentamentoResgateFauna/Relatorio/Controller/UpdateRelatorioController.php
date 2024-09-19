<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Relatorio\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\Relatorio\Requests\StoreRequest;
use App\Domain\Servico\AfugentamentoResgateFauna\Relatorio\Service\RelatorioService;
use App\Models\AfugentFaunaRelatorioModel;
use App\Shared\Http\Controllers\Controller;

class UpdateRelatorioController extends Controller
{
    public function __construct(private readonly RelatorioService $relatorioService)
    {
    }
    
    public function index(StoreRequest $request, AfugentFaunaRelatorioModel $relatorio)
    {
        $this->relatorioService->update($request->validated(), $relatorio);
        return redirect()->back()->with('message',  [
            'type' => 'info',
            'content' =>  'Relatório atualizado com sucesso'
        ]);
    }
}