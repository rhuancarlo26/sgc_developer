<?php

namespace App\Domain\Modulos\ConfiguracoesModulos\Controllers;

use App\Models\Modulo;
use App\Shared\Http\Controllers\Controller;
use App\Shared\Traits\ModelExports;
use Maatwebsite\Excel\Facades\Excel;

class GerarPlanilhaModeloController extends Controller
{
    public function gerarPlanilhaModelo(Modulo $modulo)
    {
        $campos = array_map(fn($campo) => $campo['nome_campo'], $modulo->campos);

        return Excel::download(
            new ModelExports(data: collect([]), mapping: $campos, formatUcFirst: false),
            "$modulo->nome.xlsx"
        );
    }
}
