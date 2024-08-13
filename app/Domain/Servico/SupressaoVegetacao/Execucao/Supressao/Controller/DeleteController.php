<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Controller;

use App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Services\SupressaoService;
use App\Models\AreaSupressao;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteController extends Controller
{

    public function __construct(
        private readonly SupressaoService $supressaoService,
    )
    {
    }

    public function __invoke(AreaSupressao $area): RedirectResponse
    {
        if ($this->supressaoService->delete(model: $area)) {
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
