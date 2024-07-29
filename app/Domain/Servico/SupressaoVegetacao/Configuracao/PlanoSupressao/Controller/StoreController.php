<?php

namespace App\Domain\Servico\SupressaoVegetacao\Configuracao\PlanoSupressao\Controller;

use App\Domain\Servico\SupressaoVegetacao\Configuracao\PlanoSupressao\Requests\StoreRequest;
use App\Domain\Servico\SupressaoVegetacao\Configuracao\PlanoSupressao\Services\PlanoSupressaoService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class StoreController extends Controller
{

    public function __construct(
        private readonly PlanoSupressaoService $planoSupressaoService
    )
    {
    }

    public function __invoke(StoreRequest $request): RedirectResponse
    {
        $response = $this->planoSupressaoService->store(request: $request->validated());

        return redirect()->back()->with(key: 'message', value: $response['request']);
    }

}
