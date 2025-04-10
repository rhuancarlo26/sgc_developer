<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Relatorio\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\Relatorio\Service\RelatorioService;
use App\Models\AfugentFaunaRelatorioAnexosModel;
use App\Shared\Http\Controllers\Controller;

class DestroyAnexoRelatorioController extends Controller
{
    public function __construct(private readonly RelatorioService $relatorioService) {}
    
    public function index(AfugentFaunaRelatorioAnexosModel $anexo)
    {
        return $this->relatorioService->destroyAnexo($anexo);
    }
}