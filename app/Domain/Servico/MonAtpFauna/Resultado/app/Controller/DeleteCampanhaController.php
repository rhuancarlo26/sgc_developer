<?php

namespace App\Domain\Servico\MonAtpFauna\Resultado\app\Controller;

use App\Domain\Servico\MonAtpFauna\Resultado\app\Services\ResultadoCampanhaService;
use App\Models\AtFaunaResultadoCampanha;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteCampanhaController extends Controller
{
    public function __invoke(AtFaunaResultadoCampanha $id): RedirectResponse
    {
        $id->delete(); 

        return back(303)->with('message', [
            'type' => 'success',
            'content' => 'Deletado com sucesso!',
        ]);
    }
}

