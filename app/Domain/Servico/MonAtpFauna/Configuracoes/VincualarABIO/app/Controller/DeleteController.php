<?php

namespace App\Domain\Servico\MonAtpFauna\Configuracoes\VincualarABIO\app\Controller;

use App\Models\ServicoMonAtpFaunaVincularABIO;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteController extends Controller
{

    public function __invoke(ServicoMonAtpFaunaVincularABIO $id): RedirectResponse
    {
        $id->delete();

        return redirect()->back()->with(key: 'message', value: [
            'type' => 'success',
            'content' => 'Registro deletado com sucesso!'
        ]);

    }
}
