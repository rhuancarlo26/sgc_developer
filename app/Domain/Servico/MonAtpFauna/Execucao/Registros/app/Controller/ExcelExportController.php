<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Controller;

use App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Exports\RegistroExport;
use App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Services\RegistrosService;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExcelExportController extends Controller
{
    public function __construct(
        private readonly RegistrosService $registroService,
    ) {}

    public function index(Servicos $servico, Request $request)
    {
        $searchParams = $request->all();

        $export = new RegistroExport(
            registroService: $this->registroService,
            searchParams: $searchParams,
            servico: $servico
        );

        return Excel::download($export, 'registro_atropelamento.xlsx');
    }
}
