<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Controller;

use App\Models\AtFaunaExecucaoCampanhaRet;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteRetController extends Controller
{

    public function __invoke(AtFaunaExecucaoCampanhaRet $id): RedirectResponse
    {
        $id->delete();

        return redirect()->back()->with('message', [
            'type' => 'success',
            'content' => 'RET desvinculado com sucesso!',
        ]);
    }
}
