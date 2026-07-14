<?php

namespace App\Domain\Servico\SupressaoVegetacao\Resultado\Controller;

use App\Domain\Servico\SupressaoVegetacao\Resultado\Requests\StoreRequest;
use App\Domain\Servico\SupressaoVegetacao\Resultado\Services\ResultadoService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class StoreController extends Controller
{

    public function __construct(
        private readonly ResultadoService $resultadoService
    )
    {
    }

    public function __invoke(StoreRequest $request): RedirectResponse
    {
        $response = $this->resultadoService->store(request: $request->validated());

        return redirect()->back()->with(key: 'message', value: $response['request']);
    }

}
