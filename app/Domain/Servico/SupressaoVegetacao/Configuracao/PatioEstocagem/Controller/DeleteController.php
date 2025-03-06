<?php

namespace App\Domain\Servico\SupressaoVegetacao\Configuracao\PatioEstocagem\Controller;

use App\Domain\Servico\SupressaoVegetacao\Configuracao\PatioEstocagem\Services\PatioEstocagemService;
use App\Models\PatioEstocagem;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteController extends Controller
{

    public function __construct(
        private readonly PatioEstocagemService $patioEstocagemService
    )
    {
    }

    public function __invoke(PatioEstocagem $patio): RedirectResponse
    {
        if($this->patioEstocagemService->delete(model: $patio)) {
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
