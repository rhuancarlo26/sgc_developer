<?php

namespace App\Domain\Servico\SupressaoVegetacao\Relatorio\Controller;

use App\Domain\Servico\SupressaoVegetacao\Relatorio\Requests\StoreRequest;
use App\Domain\Servico\SupressaoVegetacao\Relatorio\Services\RelatorioService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreAnexoController extends Controller
{

    public function __construct(
        private readonly RelatorioService $relatorioService,
    )
    {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $response = $this->relatorioService->storeAnexo(request: $request->all());

        return redirect()->back()->with(key: 'message', value: $response['request']);
    }

}
