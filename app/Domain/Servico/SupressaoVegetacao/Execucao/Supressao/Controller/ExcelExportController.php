<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Controller;


use App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Exports\SupressaoExport;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Services\SupressaoService;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExcelExportController extends Controller
{
    public function __construct(
        private readonly SupressaoService $supressaoService,
    )
    {
    }

    public function __invoke(Servicos $servico, Request $request): BinaryFileResponse
    {
        return Excel::download(
            export: new SupressaoExport(
                supressaoService: $this->supressaoService,
                searchParams:  $request->all('columns', 'value'),
                servicoId: $servico->id
            ),
            fileName: 'areas_suprimidas.xlsx'
        );
    }

}
