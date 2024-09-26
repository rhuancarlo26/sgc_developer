<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Controller;

use App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Requests\UpdateRequest;
use App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Services\RegistrosService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class UpdateController extends Controller
{
    public function __construct(private readonly RegistrosService $registrosService)
    {
    }

    public function __invoke(UpdateRequest $request): RedirectResponse
    {
        $response = $this->registrosService->update(request: $request->validated());

        return redirect()->back()->with('message', $response['request']);
    }
}
