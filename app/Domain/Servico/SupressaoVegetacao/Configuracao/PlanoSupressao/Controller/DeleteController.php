<?php

namespace App\Domain\Servico\SupressaoVegetacao\Configuracao\PlanoSupressao\Controller;

use App\Domain\Servico\SupressaoVegetacao\Configuracao\PlanoSupressao\Services\PlanoSupressaoService;
use App\Models\PlanoSupressao;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteController extends Controller
{

    public function __construct(
        private readonly PlanoSupressaoService $planoSupressaoService
    )
    {
    }

    public function __invoke(PlanoSupressao $plano): RedirectResponse
    {
        $this->planoSupressaoService->delete(model: $plano);

        return redirect()->back()->with(key: 'message', value: [
            'type' => 'indo',
            'message' => 'Plano de supressão deletado com sucesso!',
        ]);
    }

}
