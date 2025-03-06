<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Destinacao\Controller;

use App\Domain\Servico\SupressaoVegetacao\Execucao\Destinacao\Requests\UpdateRequest;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Destinacao\Services\DestinacaoService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class UpdateController extends Controller
{

    public function __construct(
        private readonly DestinacaoService $destinacaoService
    )
    {
    }

    public function __invoke(UpdateRequest $request): RedirectResponse
    {
        $response = $this->destinacaoService->update(request: $request->validated());

        return redirect()->back()->with(key: 'message', value: $response['request']);
    }

}
