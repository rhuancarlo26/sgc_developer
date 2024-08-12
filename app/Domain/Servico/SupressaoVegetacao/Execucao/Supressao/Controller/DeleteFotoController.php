<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Controller;

use App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Services\SupressaoService;
use App\Models\AreaSupressao;
use App\Models\Arquivo;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteFotoController extends Controller
{

    public function __construct(
        private readonly SupressaoService $supressaoService
    )
    {
    }

    public function __invoke(Arquivo $arquivo, AreaSupressao $area): RedirectResponse
    {
        $this->supressaoService->deleteFoto($arquivo, $area);
        return redirect()->back();
    }

}
