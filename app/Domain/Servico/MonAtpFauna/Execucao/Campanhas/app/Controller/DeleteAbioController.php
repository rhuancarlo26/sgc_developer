<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Controller;

use App\Models\AtFaunaExecucaoCampanhaAbio;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteAbioController extends Controller
{

    public function __invoke(AtFaunaExecucaoCampanhaAbio $id): RedirectResponse
    {
        $id->delete();

        return redirect()->back()->with('message', [
            'type' => 'success',
            'content' => 'ABIO desvinculado com sucesso!',
        ]);
    }
}
