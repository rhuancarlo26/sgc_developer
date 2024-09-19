<?php

namespace App\Domain\Servico\MonAtpFauna\Resultado\app\Controller;

use App\Domain\Servico\MonAtpFauna\Resultado\app\Requests\StoreRequest;
use App\Domain\Servico\MonAtpFauna\Resultado\app\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StoreController extends Controller
{
    public function __construct(private readonly ResultadoService $resultadoService)
    {
    }

    public function __invoke(StoreRequest $request): RedirectResponse
    {
//        dd('opa');
        $response = $this->resultadoService->store(request: $request->all());

        return redirect()->back()->with('message', $response['request']);
    }
}
