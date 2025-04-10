<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Destinacao\Controller;


use App\Domain\Servico\SupressaoVegetacao\Execucao\Destinacao\Exports\DestinacaoExport;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Destinacao\Services\DestinacaoService;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExcelExportController extends Controller
{
    public function __construct(
        private readonly DestinacaoService $destinacaoService,
    )
    {
    }

    public function __invoke(Servicos $servico, Request $request): BinaryFileResponse
    {
        return Excel::download(
            export: new DestinacaoExport(
                destinacaoService: $this->destinacaoService,
                searchParams:  $request->all('columns', 'value'),
                servicoId: $servico->id
            ),
            fileName: 'destinacao_pilhas.xlsx'
        );
    }

}
