<?php

namespace App\Domain\Servico\SupressaoVegetacao\Relatorio\Controller;

use App\Domain\Servico\SupressaoVegetacao\Relatorio\Requests\StoreRequest;
use App\Domain\Servico\SupressaoVegetacao\Relatorio\Services\RelatorioService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class StoreController extends Controller
{

    public function __construct(
        private readonly RelatorioService $relatorioService,
    )
    {
    }

    public function __invoke(StoreRequest $request): RedirectResponse
    {
        $response = $this->relatorioService->store(request: $request->validated());

        return redirect()->back()->with(key: 'message', value: $response['request']);
    }

}
