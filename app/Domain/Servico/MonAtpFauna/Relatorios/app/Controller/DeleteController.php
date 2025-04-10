<?php

namespace App\Domain\Servico\MonAtpFauna\Relatorios\app\Controller;

use App\Domain\Servico\MonAtpFauna\Relatorios\app\Services\RelatoriosService;
use App\Models\AtFaunaRelatorio;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteController extends Controller
{
    public function __construct(private readonly RelatoriosService $relatoriosService)
    {
    }

    public function __invoke(AtFaunaRelatorio $id): RedirectResponse
    {
        $this->relatoriosService->delete($id);
        return redirect()->back()->with('message', [
            'type' => 'success',
            'content' => 'Deletado com sucesso!'
        ]);
    }
}
