<?php

namespace App\Domain\Servico\PMQA\Execucao\app\Controller;

use App\Domain\Servico\PMQA\Execucao\app\Requests\UpdateRequest;
use App\Domain\Servico\PMQA\Execucao\app\Services\CampanhaService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class UpdateController extends Controller
{
    public function __construct(private readonly CampanhaService $campanhaService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, UpdateRequest $request): RedirectResponse
    {
        $response = $this->campanhaService->update($request->validated());

        return to_route('contratos.contratada.servicos.pmqa.execucao.index', [
            'contrato' => $contrato->id,
            'servico' => $servico->id
        ])->with('message', $response['request']);
    }
}
