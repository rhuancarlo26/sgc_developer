<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Controller;

use App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Requests\UpdateRequest;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Services\SupressaoService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class UpdateController extends Controller
{

    public function __construct(
        private readonly SupressaoService $supressaoService
    )
    {
    }

    public function __invoke(UpdateRequest $request): RedirectResponse
    {
        $response = $this->supressaoService->update(request: $request->all());

        return redirect()->back()->with(key: 'message', value: $response['request']);
    }

}
