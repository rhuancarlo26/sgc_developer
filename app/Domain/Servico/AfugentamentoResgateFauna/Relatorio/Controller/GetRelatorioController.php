<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Relatorio\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\Relatorio\Service\RelatorioService;
use App\Models\AfugentFaunaRelatorioModel;
use App\Shared\Http\Controllers\Controller;

class GetRelatorioController extends Controller
{
    public function __construct(private readonly RelatorioService $relatorioService) {}
    
    public function index(AfugentFaunaRelatorioModel $relatorio)
    {
        return $this->relatorioService->getAnexos($relatorio);
    }
}