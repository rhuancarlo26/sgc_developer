<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Destinacao\Controller;

use App\Domain\Servico\SupressaoVegetacao\Execucao\Destinacao\Services\DestinacaoService;
use App\Models\Destinacao;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteController extends Controller
{

    public function __construct(
        private readonly DestinacaoService $destinacaoService,
    )
    {
    }

    public function __invoke(Destinacao $destinacao): RedirectResponse
    {
        if ($this->destinacaoService->delete(model: $destinacao)) {
            return redirect()->back()->with(key: 'message', value: [
                'type' => 'info',
                'message' => 'Registro deletado com sucesso!'
            ]);
        }
        return redirect()->back()->with(key: 'message', value: [
            'type' => 'error',
            'message' => 'Erro ao deletar registro!'
        ]);
    }

}
