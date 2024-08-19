<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Controller;

use App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Services\PilhasService;
use App\Models\Arquivo;
use App\Models\ControlePilha;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteFotoController extends Controller
{

    public function __construct(
        private readonly PilhasService $pilhasService
    )
    {
    }

    public function __invoke(Arquivo $arquivo, ControlePilha $pilha): RedirectResponse
    {
        $this->pilhasService->deleteFoto($arquivo, $pilha);
        return redirect()->back();
    }

}
