<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Relatorio\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\Relatorio\Service\RelatorioService;
use App\Models\AfugentFaunaRelatorioModel;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdateConclusaoRelatorioController extends Controller
{
    public function __construct(private readonly RelatorioService $relatorioService)
    {
    }
    
    public function index(Request $request, AfugentFaunaRelatorioModel $relatorio)
    {
        return $this->relatorioService->updateConclusao($request, $relatorio);
    }
}