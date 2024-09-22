<?php

namespace App\Domain\Servico\MonAtpFauna\Relatorios\app\Controller;

use App\Domain\Servico\MonAtpFauna\Relatorios\app\Requests\StoreRequest;
use App\Domain\Servico\MonAtpFauna\Relatorios\app\Services\RelatoriosService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class EnviarFiscalController extends Controller
{
    public function __construct(private readonly RelatoriosService $relatoriosService)
    {
    }

    public function __invoke(StoreRequest $request): RedirectResponse
    {
        $response = $this->relatoriosService->update(request: [
            ...$request->all(),
            'fk_status' => 2,
        ]);
        return redirect()->back()->with('message', $response['request']);
    }
}
