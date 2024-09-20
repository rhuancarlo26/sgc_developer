<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Controller;

use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Services\CampanhasService;
use App\Models\AtFaunaExecucaoCampanha;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteController extends Controller
{
    public function __construct(private readonly CampanhasService $campanhasService)
    {
    }

    public function __invoke(AtFaunaExecucaoCampanha $campanha): RedirectResponse
    {
        $this->campanhasService->delete($campanha);

        return redirect()->back()->with('message', [
            'type' => 'success',
            'content' => 'Campanha excluída com sucesso!',
        ]);
    }
}
