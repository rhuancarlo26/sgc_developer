<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Controller;


use App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Exports\PilhasExport;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Services\PilhasService;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExcelExportController extends Controller
{
    public function __construct(
        private readonly PilhasService $pilhasService,
    )
    {
    }

    public function __invoke(Servicos $servico, Request $request): BinaryFileResponse
    {
        return Excel::download(
            export: new PilhasExport(
                pilhasService: $this->pilhasService,
                searchParams:  $request->all('columns', 'value'),
                servicoId: $servico->id
            ),
            fileName: 'controle_pilhas.xlsx'
        );
    }

}
