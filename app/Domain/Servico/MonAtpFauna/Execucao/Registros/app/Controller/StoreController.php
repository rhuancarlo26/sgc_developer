<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Controller;

use App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Requests\StoreRequest;
use App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Services\RegistrosService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class StoreController extends Controller
{
    public function __construct(private readonly RegistrosService $registrosService)
    {
    }

    public function __invoke(StoreRequest $request): RedirectResponse
    {
        $response = $this->registrosService->store(request: $request->validated());

        return redirect()->back()->with('message', $response['request']);
    }
}
