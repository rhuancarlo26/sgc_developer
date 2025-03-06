<?php

namespace App\Domain\Servico\PassagemFauna\Configuracao\VincularABIO\Controller;

use App\Domain\Servico\PassagemFauna\Configuracao\VincularABIO\Services\VincularABIOService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BuscarLicencaController extends Controller
{
    public function __construct(private readonly VincularABIOService $vincularABIO)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, Request $request)
    {
        $response = $this->vincularABIO->buscarLicenca(numero_licenca: $request->numero_licenca);

        return response()->json($response);
    }
}
