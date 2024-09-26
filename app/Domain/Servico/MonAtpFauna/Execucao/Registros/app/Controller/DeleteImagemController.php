<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Controller;

use App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Services\RegistrosImagensService;
use App\Models\AtFaunaExecucaoRegistroImagem;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteImagemController extends Controller
{
    public function __construct(private readonly RegistrosImagensService $registrosImagemService)
    {
    }

    public function __invoke(AtFaunaExecucaoRegistroImagem $imagem): RedirectResponse
    {
        $this->registrosImagemService->delete(model: $imagem);

        return redirect()->back()->with('message', [
            'type' => 'success',
            'content' => 'Imagem excluída com sucesso!',
        ]);
    }
}
