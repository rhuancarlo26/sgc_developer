<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Controller;

use App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Requests\StoreRequest;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Services\PilhasService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class StoreController extends Controller
{

    public function __construct(
        private readonly PilhasService $pilhasService
    )
    {
    }

    public function __invoke(StoreRequest $request): RedirectResponse
    {
        $response = $this->pilhasService->store(request: $request->validated());

        return redirect()->back()->with(key: 'message', value: $response['request']);
    }

}
