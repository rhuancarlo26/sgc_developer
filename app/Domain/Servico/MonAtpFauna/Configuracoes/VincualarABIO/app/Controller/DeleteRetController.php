<?php

namespace App\Domain\Servico\MonAtpFauna\Configuracoes\VincualarABIO\app\Controller;

use App\Models\ServicoMonAtpFaunaVincularRetABIO;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteRetController extends Controller
{

    public function __invoke(ServicoMonAtpFaunaVincularRetABIO $ret): RedirectResponse
    {
        $ret->delete();

        return redirect()->back()->with(key: 'message', value: [
            'type' => 'success',
            'content' => 'Registro deletado com sucesso!'
        ]);

    }
}
