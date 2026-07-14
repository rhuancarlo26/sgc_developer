<?php

namespace App\Domain\Servico\SupressaoVegetacao\Configuracao\PatioEstocagem\Controller;

use App\Domain\Servico\SupressaoVegetacao\Configuracao\PatioEstocagem\Requests\UpdateRequest;
use App\Domain\Servico\SupressaoVegetacao\Configuracao\PatioEstocagem\Services\PatioEstocagemService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class UpdateController extends Controller
{

    public function __construct(
        private readonly PatioEstocagemService $patioEstocagemService
    )
    {
    }

    public function __invoke(UpdateRequest $request): RedirectResponse
    {
        $response = $this->patioEstocagemService->update(request: $request->all());

        return redirect()->back()->with(key: 'message', value: $response['request']);
    }

}
