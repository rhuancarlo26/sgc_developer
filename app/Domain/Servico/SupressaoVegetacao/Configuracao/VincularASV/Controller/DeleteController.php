<?php

namespace App\Domain\Servico\SupressaoVegetacao\Configuracao\VincularASV\Controller;

use App\Domain\Servico\SupressaoVegetacao\Configuracao\VincularASV\Services\VincularASVService;
use App\Models\Contrato;
use App\Models\Licenca;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteController extends Controller
{
    public function __construct(
        private readonly VincularASVService $vincularASVService
    )
    {
    }

    public function __invoke(Contrato $contrato, Servicos $servico, Licenca $licenca): RedirectResponse
    {
        $this->vincularASVService->deleteByLicencaAndServico($licenca, $servico);

        return redirect()->back()->with('message', [
            'type' => 'info',
            'content' => 'Licença removida!'
        ]);
    }

}
