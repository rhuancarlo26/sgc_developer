<?php

namespace App\Domain\Sgc\Contratada\Modulo\ConfigPlanilha\Controllers;

use App\Models\SgcModulo;
use App\Shared\Http\Controllers\Controller;
use App\Shared\Traits\ModelExports;
use Maatwebsite\Excel\Facades\Excel;

class GerarPlanilhaModeloController extends Controller
{
    public function gerarPlanilhaModelo($contrato, $produto, SgcModulo $modulo)
    {
        $campos = array_map(fn($campo) => $campo['nome_campo'], $modulo->campos);

        return Excel::download(
            new ModelExports(collect([]), $campos),
            "$modulo->nome.xlsx"
        );
    }
}
