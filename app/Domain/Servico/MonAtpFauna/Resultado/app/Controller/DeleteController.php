<?php

namespace App\Domain\Servico\MonAtpFauna\Resultado\app\Controller;

use App\Domain\Servico\MonAtpFauna\Resultado\app\Services\ResultadoService;
use App\Models\AtFaunaResultado;
use App\Models\AtFaunaResultadoCampanha;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteController extends Controller
{
    public function __construct(private readonly ResultadoService $resultadoService)
    {
    }

    public function __invoke(AtFaunaResultado $id): RedirectResponse
    {
        $this->resultadoService->delete(model: $id);
        AtFaunaResultadoCampanha::where('fk_resultado', $id)->delete();

        return redirect()->back()->with('message', [
            'type' => 'success',
            'content' => 'Deletado com sucesso!',
        ]);
    }
}
