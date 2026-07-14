<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Controller;

use App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Services\RegistrosService;
use App\Models\AtFaunaExecucaoRegistro;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteController extends Controller
{
    public function __construct(private readonly RegistrosService $registrosService)
    {
    }

    public function __invoke(AtFaunaExecucaoRegistro $registro): RedirectResponse
    {
        $this->registrosService->delete(model: $registro);

        return redirect()->back()->with('message', [
            'type' => 'success',
            'content' => 'Registro excluído com sucesso!',
        ]);
    }
}
