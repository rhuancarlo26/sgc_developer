<?php

namespace App\Domain\Servico\MonAtpFauna\Relatorios\app\Controller;

use App\Domain\Servico\MonAtpFauna\Relatorios\app\Requests\StoreRequest;
use App\Domain\Servico\MonAtpFauna\Relatorios\app\Services\RelatoriosService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StoreController extends Controller
{
    public function __construct(private readonly RelatoriosService $relatoriosService)
    {
    }

    public function __invoke(StoreRequest $request): RedirectResponse
    {
        $response = $this->relatoriosService->store(request: $request->all());
        return redirect()->back()->with('message', $response['request']);
    }
}
