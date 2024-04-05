<?php

namespace App\Domain\Contrato\Contratada\Servico\app\Controller;

use App\Domain\Contrato\Contratada\Servico\app\Services\ServicoService;
use App\Models\Contrato;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexServicosContratadaController extends Controller
{
    public function __construct(private readonly ServicoService $servicoService)
    {
    }

    public function index(Contrato $contrato, Request $request): Response
    {
        $searchParams = $request->all('searchColumn', 'searchValue');

        $response = $this->servicoService->listarServicos($contrato, $searchParams);

        return Inertia::render('Contrato/Contratada/Servicos/Index', [
            'contrato' => $contrato,
            ...$response
        ]);
    }
}