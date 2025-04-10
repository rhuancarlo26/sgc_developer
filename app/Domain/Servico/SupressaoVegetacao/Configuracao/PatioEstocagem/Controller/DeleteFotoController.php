<?php

namespace App\Domain\Servico\SupressaoVegetacao\Configuracao\PatioEstocagem\Controller;

use App\Domain\Servico\SupressaoVegetacao\Configuracao\PatioEstocagem\Services\PatioEstocagemService;
use App\Models\Arquivo;
use App\Models\PatioEstocagem;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteFotoController extends Controller
{

    public function __construct(
        private readonly PatioEstocagemService $patioEstocagemService
    )
    {
    }

    public function __invoke(Arquivo $arquivo, PatioEstocagem $patio): RedirectResponse
    {
        if($this->patioEstocagemService->deleteFoto($arquivo, $patio)) {
            return back()->with('message', [
                'type'    => 'info',
                'content' => 'Deletado!',
            ]);
        }
        return redirect()->back()->with('message', [
            'type'    => 'error',
            'content' => "Erro ao tentar deletar, tente novamente!",
        ]);
    }

}
