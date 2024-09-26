<?php

namespace App\Domain\Servico\MonAtpFauna\Resultado\app\Controller;

use App\Domain\Servico\MonAtpFauna\Resultado\app\Services\ResultadoCampanhaService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreCampanhaController extends Controller
{
    public function __construct(private readonly ResultadoCampanhaService $resultadoService)
    {
    }

    public function __invoke(Request $request): RedirectResponse
    {

        $response = $this->resultadoService->store(request: $request->all());

        return redirect()->back()->with('message', $response['request']);
    }
}
