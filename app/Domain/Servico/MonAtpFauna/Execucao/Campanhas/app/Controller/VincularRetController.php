<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Controller;

use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Services\ExecucaoCampanhaRetService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VincularRetController extends Controller
{
    public function __construct(private readonly ExecucaoCampanhaRetService $execucaoCampanhaRetService)
    {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $response = $this->execucaoCampanhaRetService->vincularRet(request: $request->all());

        return redirect()->back()->with('message', $response['request']);
    }
}
