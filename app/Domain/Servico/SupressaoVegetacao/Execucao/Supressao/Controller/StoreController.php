<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Controller;

use App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Requests\StoreRequest;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Services\SupressaoService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class StoreController extends Controller
{

    public function __construct(
        private readonly SupressaoService $supressaoService
    )
    {
    }

    public function __invoke(StoreRequest $request): RedirectResponse
    {
        $response = $this->supressaoService->store(request: $request->validated());

        return redirect()->back()->with(key: 'message', value: $response['request']);
    }

}
