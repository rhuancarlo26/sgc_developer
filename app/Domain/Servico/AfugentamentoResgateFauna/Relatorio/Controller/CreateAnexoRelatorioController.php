<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Relatorio\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\Relatorio\Service\RelatorioService;
use App\Models\AfugentFaunaRelatorioModel;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CreateAnexoRelatorioController extends Controller
{
    public function __construct(private readonly RelatorioService $relatorioService) {}

    public function index(AfugentFaunaRelatorioModel $relatorio, Request $request)
    {
        $this->relatorioService->createAnexo($relatorio, $request->file('arquivo'));
        return redirect()->back()->with('message',  [
            'type' => 'info',
            'content' =>  'Relatório atualizado com sucesso'
        ]);
    }
}
