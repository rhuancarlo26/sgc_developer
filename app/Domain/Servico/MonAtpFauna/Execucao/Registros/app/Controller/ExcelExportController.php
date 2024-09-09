<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Controller;

use App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Exports\RegistroExport;
use App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Services\RegistrosService;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExcelExportController extends Controller
{
    public function __construct(
        private readonly RegistrosService $registroService,
    )
    {
    }

    public function __invoke(Servicos $servico, Request $request): BinaryFileResponse
    {
        return Excel::download(
            export: new RegistroExport(
                registroService: $this->registroService,
                searchParams:  $request->all('columns', 'value'),
                servico: $servico
            ),
            fileName: 'registro_atropelamento.xlsx'
        );
    }

}
