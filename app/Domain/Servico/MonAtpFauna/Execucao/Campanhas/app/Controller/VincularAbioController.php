<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Controller;

use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Services\ExecucaoCampanhaService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VincularAbioController extends Controller
{
    public function __construct(private readonly ExecucaoCampanhaService $execucaoCampanhaService)
    {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $response = $this->execucaoCampanhaService->vincularAbio(request: $request->all());

        return redirect()->back()->with('message', $response['request']);
    }
}
