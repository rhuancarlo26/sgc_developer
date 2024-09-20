<?php

namespace App\Domain\Servico\ContOcorrencia\Configuracao\Empreendimento\Controller;

use App\Domain\Licenca\Shapefile\Services\LicencaShapefileService;
use App\Domain\Servico\ContOcorrencia\Configuracao\Empreendimento\Requests\StoreLicencaShapefileRequest;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;

class StoreLicencaShapefileController extends Controller
{
    public function __construct(private readonly LicencaShapefileService $licencaShapefileService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, StoreLicencaShapefileRequest $request)
    {
        $response = $this->licencaShapefileService->store($request->validated());

        return to_route('contratos.contratada.servicos.cont_ocorrencia.configuracao.empreendimento.index', ['contrato' => $contrato->id, 'servico' => $servico->id])->with('message', $response['request']);
    }
}
