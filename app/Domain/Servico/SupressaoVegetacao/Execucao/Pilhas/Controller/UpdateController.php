<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Controller;

use App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Requests\UpdateRequest;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Services\PilhasService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class UpdateController extends Controller
{

    public function __construct(
        private readonly PilhasService $pilhasService
    )
    {
    }

    public function __invoke(UpdateRequest $request): RedirectResponse
    {
        $response = $this->pilhasService->update(request: $request->validated());

        return redirect()->back()->with(key: 'message', value: $response['request']);
    }

}
