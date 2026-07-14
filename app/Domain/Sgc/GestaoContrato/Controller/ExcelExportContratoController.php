<?php

namespace App\Domain\Sgc\GestaoContrato\Controller;

use App\Domain\Contrato\GestaoContrato\Services\ListagemContratoService;
use App\Shared\Http\Controllers\Controller;
use App\Shared\Traits\ModelExports;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExcelExportContratoController extends Controller
{
    public function __construct(private readonly ListagemContratoService $listagemContrato)
    {
    }

    /**
     * Download de excel
     * @param Illuminate\Http\Request $request
     * @return Symfony\Component\HttpFoundation\BinaryFileResponse;
     */
    public function excelExport(Request $request): BinaryFileResponse
    {
        $searchParams = $request->all('searchColumn', 'searchValue');

        $contratos = $this->listagemContrato
            ->search(...$searchParams)
            ->paginate()
            ->appends($searchParams);

        return Excel::download(
            new ModelExports($contratos, [
                'ufs', 'brs', 'numero_contrato', 'cnpj', 'contratada', 'processo_sei', 'situacao', 'created_at', 'updated_at'
            ]),
            'Contratos.xlsx'
        );
    }
}