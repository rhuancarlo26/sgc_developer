<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Controller;

use App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Services\CorteEspecieService;
use App\Models\CorteEspecie;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteCorteEspecieController extends Controller
{

    public function __construct(
        private readonly CorteEspecieService $corteEspecieService,
    )
    {
    }

    public function __invoke(CorteEspecie $corte): RedirectResponse
    {
        $this->corteEspecieService->delete(model: $corte);
        return redirect()->back();
    }

}
