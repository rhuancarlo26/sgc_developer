<?php

namespace App\Domain\Sgc\Contratada\RelatorioCoord\Controller;

use App\Domain\Sgc\Contratada\RelatorioCoord\Services\CreateRelatorioService;
use Illuminate\Http\Request;

class CreateController
{
    protected $createRelatorioService;

    public function __construct(CreateRelatorioService $createRelatorioService)
    {
        $this->createRelatorioService = $createRelatorioService;
    }

    public function index(Request $request)
    {
        // Receba o contrato do request
        $contrato = $request->input('contrato');

        // Passe o contrato para o service
        $this->createRelatorioService->iniciarNovoRelatorio($contrato);
                  
    }
}
