<?php

namespace App\Domain\Servico\SupressaoVegetacao\Configuracao\PlanoSupressao\Controller;

use App\Domain\Servico\SupressaoVegetacao\Configuracao\PlanoSupressao\Requests\StoreRequest;
use App\Domain\Servico\SupressaoVegetacao\Configuracao\PlanoSupressao\Services\PlanoSupressaoService;
use App\Models\PlanoSupressao;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreController extends Controller
{

    public function __construct(
        private readonly PlanoSupressaoService $planoSupressaoService
    ) {}

    public function __invoke(StoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $this->planoSupressaoService->store($request);

        $msg = isset($validated['id']) ? 'Plano atualizado com sucesso!' : 'Plano cadastrado com sucesso!';

        return redirect()->back()->with('message', $msg);
    }
}
