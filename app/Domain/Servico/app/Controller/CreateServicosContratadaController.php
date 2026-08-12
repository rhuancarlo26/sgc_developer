<?php

namespace App\Domain\Servico\app\Controller;

use App\Domain\Servico\app\Services\ServicoService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CreateServicosContratadaController extends Controller
{
    public function __construct(private readonly ServicoService $servicoService) {}

    public function index(Request $request, Contrato $contrato, Servicos $servico): Response
    {
        $response = $this->servicoService->createServicos($contrato, $servico);

        $podeVoltarConfeccao = $request->user()?->hasAnyRole([
            'Super Admin',
            'Administrador',
        ]) ?? false;

        return Inertia::render('Contrato/Contratada/Servicos/Form', [
            'contrato' => $contrato,
            'modoVoltarConfeccao' => $request->query('acao') === 'voltar-confeccao',
            'podeVoltarConfeccao' => $podeVoltarConfeccao,
            ...$response
        ]);
    }
}
