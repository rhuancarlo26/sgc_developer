<?php

namespace App\Domain\Servico\MonAtpFauna\Resultado\app\Controller;

use App\Domain\Servico\MonAtpFauna\Resultado\app\Requests\StoreRequest;
use App\Domain\Servico\MonAtpFauna\Resultado\app\Services\ResultadoService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class UpdateController extends Controller
{
    public function __construct(private readonly ResultadoService $resultadoService)
    {
    }

    public function __invoke(StoreRequest $request): RedirectResponse
    {
        $response = $this->resultadoService->update(request: $request->all());

        return redirect()->back()->with('message', $response['request']);
    }
}
