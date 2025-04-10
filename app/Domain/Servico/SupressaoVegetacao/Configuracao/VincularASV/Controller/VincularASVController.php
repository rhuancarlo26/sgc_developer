<?php

namespace App\Domain\Servico\SupressaoVegetacao\Configuracao\VincularASV\Controller;

use App\Domain\Servico\SupressaoVegetacao\Configuracao\VincularASV\Requests\VincularASVRequest;
use App\Domain\Servico\SupressaoVegetacao\Configuracao\VincularASV\Services\VincularASVService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class VincularASVController extends Controller
{
    public function __construct(
        private readonly VincularASVService $vincularASVService
    )
    {
    }

    public function __invoke(VincularASVRequest $request): RedirectResponse
    {
        $this->vincularASVService->store(request: $request->all());
        return redirect()->back();
    }

}
