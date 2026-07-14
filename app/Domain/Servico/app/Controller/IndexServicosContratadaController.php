<?php

namespace App\Domain\Servico\app\Controller;

use App\Domain\Servico\app\Services\ServicoService;
use App\Models\Contrato;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexServicosContratadaController extends Controller
{
    public function __construct(private readonly ServicoService $servicoService) {}

    public function index(Contrato $contrato, Request $request): Response
    {
        $searchParams = $request->all('searchColumn', 'searchValue');

        $filtros = $request->only([
            'filtro_tema_id',
            'filtro_servico_id',
            'filtro_status_aprovacao',
        ]);

        $response = $this->servicoService->listarServicos($contrato, $searchParams, $filtros);

        $podeVoltarConfeccao = $request->user()?->hasAnyRole([
            'Super Admin',
            'Administrador',
        ]) ?? false;

        return Inertia::render('Contrato/Contratada/Servicos/Index', [
            'contrato' => $contrato,
            'podeVoltarConfeccao' => $podeVoltarConfeccao,
            ...$response
        ]);
    }
}
