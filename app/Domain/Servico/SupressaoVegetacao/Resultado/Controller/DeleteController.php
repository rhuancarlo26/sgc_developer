<?php

namespace App\Domain\Servico\SupressaoVegetacao\Resultado\Controller;

use App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Services\PilhasService;
use App\Models\ResultadoSupressao;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteController extends Controller
{

    public function __construct(
        private readonly PilhasService $pilhasService,
    )
    {
    }

    public function __invoke(ResultadoSupressao $resultado): RedirectResponse
    {
        if ($this->pilhasService->delete(model: $resultado)) {
            return redirect()->back()->with(key: 'message', value: [
                'type' => 'success',
                'message' => 'Registro deletado com sucesso!'
            ]);
        }
        return redirect()->back()->with(key: 'message', value: [
            'type' => 'error',
            'message' => 'Erro ao deletar registro!'
        ]);
    }

}
