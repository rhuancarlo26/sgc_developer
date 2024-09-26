<?php

namespace App\Domain\Servico\MonAtpFauna\Configuracoes\MalhaViaria\app\Controller;

use App\Domain\Servico\MonAtpFauna\Configuracoes\MalhaViaria\app\Services\MalhaViariaService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EnviarFiscalController extends Controller
{
    public function __construct(private readonly MalhaViariaService $malhaViariaService)
    {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $this->malhaViariaService->sendFiscal(servico: $request->get('servico_id'));

        return redirect()->back()->with('message', [
                'type' => 'success',
                'content' => 'Configurações enviadas para o fiscal!',
        ]);
    }
}
