<?php

namespace App\Domain\Servico\SupressaoVegetacao\Relatorio\Controller;

use App\Domain\Servico\SupressaoVegetacao\Relatorio\Services\RelatorioService;
use App\Models\SupressaoRelatorio;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteController extends Controller
{

    public function __construct(
        private readonly RelatorioService $relatorioService,
    )
    {
    }

    public function __invoke(SupressaoRelatorio $relatorio): RedirectResponse
    {
        if ($this->relatorioService->delete(model: $relatorio)) {
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
